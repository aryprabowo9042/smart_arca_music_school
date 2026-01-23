<?php
header('Content-Type: application/json');

// Mengambil input dari JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$pesanUser = $input['pesan'] ?? '';

// Mengambil API Key dari Environment Variable Vercel
$api_key = getenv('GROQ_API_KEY'); 
$url = "https://api.groq.com/openai/v1/chat/completions";

if (empty($pesanUser)) {
    echo json_encode(['jawaban' => 'Ada yang bisa saya bantu?']);
    exit;
}

$data = [
    "model" => "llama3-8b-8192",
    "messages" => [
        [
            "role" => "system", 
            "content" => "Anda adalah admin ramah dari Smart Arca Music School. Jawablah pertanyaan calon murid dengan sopan dan singkat."
        ],
        ["role" => "user", "content" => $pesanUser]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Penting untuk kelancaran di beberapa server

$response = curl_exec($ch);
$result = json_decode($response, true);
$jawaban = $result['choices'][0]['message']['content'] ?? "Maaf, sistem sedang sibuk.";

echo json_encode(['jawaban' => $jawaban]);
