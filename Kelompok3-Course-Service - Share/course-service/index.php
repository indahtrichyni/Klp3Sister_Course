<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/db.php";

function respond($status, $body) {
    http_response_code($status);
    echo json_encode($body);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Menemukan prefix /api/courses,
// baik di URL langsung maupun URL dengan prefix folder.
$parts = array_values(array_filter(
    explode("/", trim($path, "/")),
    "strlen"
));

$apiIndex = array_search("api", $parts, true);

if (
    $method === "GET" &&
    $apiIndex !== false &&
    isset($parts[$apiIndex + 1]) &&
    $parts[$apiIndex + 1] === "health"
) {
    respond(200, [
        "success" => true,
        "status" => "UP",
        "service" => "course-service"
    ]);
}

if (
    $apiIndex === false ||
    !isset($parts[$apiIndex + 1]) ||
    $parts[$apiIndex + 1] !== "courses"
) {
    respond(404, [
        "success" => false,
        "message" => "Endpoint tidak ditemukan"
    ]);
}

$remaining = array_slice($parts, $apiIndex + 2);

if (count($remaining) > 1) {
    respond(404, [
        "success" => false,
        "message" => "Endpoint tidak ditemukan"
    ]);
}

$id = null;

if (count($remaining) === 1) {
    if (
        !ctype_digit($remaining[0]) ||
        (int)$remaining[0] < 1
    ) {
        respond(400, [
            "success" => false,
            "message" => "ID tidak valid"
        ]);
    }

    $id = (int)$remaining[0];
}

// GET /api/courses
if ($method === "GET" && $id === null) {
    $result = $conn->query(
        "SELECT id, code, name, credits
         FROM courses ORDER BY id"
    );

    if (!$result) {
        respond(500, [
            "success" => false,
            "message" => "Gagal mengambil data"
        ]);
    }

    $courses = [];

    while ($row = $result->fetch_assoc()) {
        $row["id"] = (int)$row["id"];
        $row["credits"] = (int)$row["credits"];
        $courses[] = $row;
    }

    respond(200, [
        "success" => true,
        "data" => $courses
    ]);
}

// GET /api/courses/{id}
if ($method === "GET" && $id !== null) {
    $stmt = $conn->prepare(
        "SELECT id, code, name, credits
         FROM courses WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $course = $result->fetch_assoc();

    if (!$course) {
        respond(404, [
            "success" => false,
            "message" => "Course tidak ditemukan"
        ]);
    }

    $course["id"] = (int)$course["id"];
    $course["credits"] = (int)$course["credits"];

    respond(200, [
        "success" => true,
        "data" => $course
    ]);
}

// POST /api/courses
if ($method === "POST" && $id === null) {
    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (
        !is_array($input) ||
        !isset($input["code"], $input["name"], $input["credits"]) ||
        !is_string($input["code"]) ||
        !is_string($input["name"]) ||
        !is_int($input["credits"]) ||
        trim($input["code"]) === "" ||
        trim($input["name"]) === "" ||
        strlen($input["code"]) > 20 ||
        strlen($input["name"]) > 100 ||
        $input["credits"] < 1 ||
        $input["credits"] > 255
    ) {
        respond(400, [
            "success" => false,
            "message" => "Data course tidak valid"
        ]);
    }

    $code = trim($input["code"]);
    $name = trim($input["name"]);
    $credits = $input["credits"];

    $stmt = $conn->prepare(
        "INSERT INTO courses (code, name, credits)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param("ssi", $code, $name, $credits);

    if (!$stmt->execute()) {
        if ($stmt->errno === 1062) {
            respond(409, [
                "success" => false,
                "message" => "Kode course sudah digunakan"
            ]);
        }

        respond(500, [
            "success" => false,
            "message" => "Gagal menambahkan course"
        ]);
    }

    respond(201, [
        "success" => true,
        "message" => "Course berhasil ditambahkan",
        "data" => [
            "id" => $conn->insert_id,
            "code" => $code,
            "name" => $name,
            "credits" => $credits
        ]
    ]);
}

// DELETE /api/courses/{id}
if ($method === "DELETE" && $id !== null) {
    $stmt = $conn->prepare(
        "DELETE FROM courses WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        respond(404, [
            "success" => false,
            "message" => "Course tidak ditemukan"
        ]);
    }

    respond(200, [
        "success" => true,
        "message" => "Course berhasil dihapus"
    ]);
}

// PATCH /api/courses/{id} - ubah sebagian data saja
// Field yang tidak dikirim dibiarkan tetap seperti semula.
// Contoh: {"name":"Basis Data Lanjut"} hanya mengubah name,
// code dan credits tetap.
if ($method === "PATCH" && $id !== null) {
    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!is_array($input) || empty($input)) {
        respond(400, [
            "success" => false,
            "message" => "Data course tidak valid"
        ]);
    }

    $stmt = $conn->prepare(
        "SELECT id, code, name, credits
         FROM courses WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $old = $stmt->get_result()->fetch_assoc();

    if (!$old) {
        respond(404, [
            "success" => false,
            "message" => "Course tidak ditemukan"
        ]);
    }

    $code = isset($input["code"]) ? trim($input["code"]) : $old["code"];
    $name = isset($input["name"]) ? trim($input["name"]) : $old["name"];
    $credits = isset($input["credits"]) ? $input["credits"] : (int)$old["credits"];

    if (
        !is_string($code) ||
        !is_string($name) ||
        !is_int($credits) ||
        $code === "" ||
        $name === "" ||
        strlen($code) > 20 ||
        strlen($name) > 100 ||
        $credits < 1 ||
        $credits > 255
    ) {
        respond(400, [
            "success" => false,
            "message" => "Data course tidak valid"
        ]);
    }

    $stmt = $conn->prepare(
        "UPDATE courses SET code = ?, name = ?, credits = ?
         WHERE id = ?"
    );

    $stmt->bind_param("ssii", $code, $name, $credits, $id);

    if (!$stmt->execute()) {
        if ($stmt->errno === 1062) {
            respond(409, [
                "success" => false,
                "message" => "Kode course sudah digunakan"
            ]);
        }

        respond(500, [
            "success" => false,
            "message" => "Gagal mengubah course"
        ]);
    }

    respond(200, [
        "success" => true,
        "message" => "Course berhasil diubah",
        "data" => [
            "id" => $id,
            "code" => $code,
            "name" => $name,
            "credits" => $credits
        ]
    ]);
}

// Method tidak diizinkan pada endpoint tersebut
if ($method === "POST" && $id !== null) {
    header("Allow: GET, PATCH, DELETE");
    respond(405, [
        "success" => false,
        "message" => "Method tidak diizinkan"
    ]);
}

if ($method === "DELETE" && $id === null) {
    header("Allow: GET, POST");
    respond(405, [
        "success" => false,
        "message" => "Method tidak diizinkan"
    ]);
}

header("Allow: GET, POST, PATCH, DELETE");
respond(405, [
    "success" => false,
    "message" => "Method tidak diizinkan"
]);