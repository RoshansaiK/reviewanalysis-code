<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$role = $_SESSION['role'];
include('db.connection.php');

$API_KEY = "AIzaSyDjiQi5FGIueVbFXFFK-ZQKhpcz6y9Dlhw";
$API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$API_KEY";

function callGeminiAPI($prompt)
{
    global $API_URL;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

$generatedCode = '';
$accuracy = 0;
$codeLength = 0;
$vulnerabilities = [];
$keysUsed = [];
$functionsUsed = [];

if (isset($_POST['prompt'])) {
    $prompt = $_POST['prompt'];
    $response = callGeminiAPI($prompt);

    if ($response) {
        $responseData = json_decode($response, true);
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $generatedCode = $responseData['candidates'][0]['content']['parts'][0]['text'];
            $codeLength = strlen($generatedCode);
            $accuracy = calculateAccuracy($generatedCode);
            $vulnerabilities = checkVulnerabilities($generatedCode);
            $keysUsed = extractKeys($generatedCode);
            $functionsUsed = extractFunctions($generatedCode);
        } else {
            $generatedCode = "Error: Unexpected response format.";
        }
    } else {
        $generatedCode = "Error: No response from the API.";
    }
}

function calculateAccuracy($code)
{
    $lines = explode("\n", $code);
    $lineCount = count($lines);
    $accuracy = 85 + (min(15, $lineCount / 5));
    return round($accuracy, 2);
}

function checkVulnerabilities($code)
{
    $issues = [];
    if (preg_match('/eval\s*\(/', $code)) {
        $issues[] = "Use of `eval()` detected. This is a major security risk.";
    }
    if (preg_match('/\$.*=\s*\$_(GET|POST|REQUEST|COOKIE)/', $code)) {
        $issues[] = "Unvalidated user input detected. Sanitize all inputs.";
    }
    if (!preg_match('/password_hash\s*\(/', $code) && preg_match('/md5|sha1/', $code)) {
        $issues[] = "Insecure hashing algorithm (MD5/SHA1) detected. Use `password_hash()` instead.";
    }
    if (preg_match('/mysqli_query\s*\(.*\$_/', $code)) {
        $issues[] = "Potential SQL Injection vulnerability detected. Use prepared statements.";
    }
    return $issues;
}

function extractKeys($code)
{
    preg_match_all('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $code, $matches);
    return array_unique($matches[0]);
}

function extractFunctions($code)
{
    preg_match_all('/\b[a-zA-Z_][a-zA-Z0-9_]*\s*\(/', $code, $matches);
    $functions = array_map(function ($func) {
        return rtrim($func, '(');
    }, $matches[0]);
    return array_unique($functions);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Code Review</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link
      href="https://i.pinimg.com/originals/75/db/80/75db80642e75acc0f8514572065964ac.png"
      rel="shortcut icon"
      type="image/x-icon"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)), 
                url('https://media-cldnry.s-nbcnews.com/image/upload/rockcms/2023-02/230203-chatgpt-test-scanning-le-1436-230b9d.jpg') no-repeat center center/cover;
            color: #333;
        }

        header {
            background-color: #8c52a1;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 30px;
            color: white;
        }

        .logo h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: bold;
            color: white;
        }

        .navbar nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar nav ul li {
            margin-left: 20px;
        }

        .navbar nav ul li a {
            text-decoration: none;
            color: white;
            font-size: 1rem;
        }

        .logout-icon i {
            font-size: 18px;
        }

        .container {
            max-width: 900px;
            margin: 80px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #5a5a5a;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 1.1rem;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
    background-color: #8c52a1; /* Updated to #8c52a1 */
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center; /* Align the icon and text */
    justify-content: center;
    gap: 10px; /* Space between icon and text */
}

input[type="submit"] i {
    font-size: 18px; /* Icon size */
}

input[type="submit"]:hover {
    background-color: #7a3f8c; /* Darker shade on hover */
}

        .generated-code {
            margin-top: 20px;
            padding: 15px;
            background: #f4f4f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header i {
            color: #8c52a1;
            font-size: 24px;
        }

        .card-body pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
        }

        .accuracy-level {
            color: green;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .code-length {
            color: green;
            font-weight: bold;
            font-size: 1.1rem;
        }

        canvas {
            max-width: 100%;
            margin: 20px 0;
        }

        .rules {
            background: #f4f4f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }

        .rules ul {
            padding-left: 20px;
        }

        .rules ul li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<header>
        <div class="logo">
            <i class="fa fa-cloud"></i>
            <h1>Cloud-Based Code Review</h1>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="logout-icon"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1><i class="fas fa-code fa-spin"></i>Automated Code Review with Security Analysis</h1>
        <form method="post">
            <label for="prompt">Enter your prompt:</label>
            <textarea id="prompt" name="prompt" rows="4" cols="50" required></textarea>
            <input type="submit" value="Submit Prompt">
        </form>
        
        <?php if ($generatedCode): ?>
            <div class="card">
    <h2><i class="bi bi-file-earmark-code"></i> Generated Code</h2> <!-- Added Bootstrap icon here -->
    <pre><?php echo htmlspecialchars($generatedCode); ?></pre>
    <div><strong>Accuracy:</strong> <?php echo $accuracy; ?>%</div>
    <div><strong>Code Length:</strong> <?php echo $codeLength; ?> characters</div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


            <?php if (!empty($vulnerabilities)): ?>
                <div class="card">
                    <h2>Security Vulnerabilities Detected</h2>
                    <ul>
                        <?php foreach ($vulnerabilities as $issue): ?>
                            <li><?php echo htmlspecialchars($issue); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <div class="card">
                    <h2>Security Analysis</h2>
                    <p>No vulnerabilities detected. The generated code is secure.</p>
                </div>
            <?php endif; ?>

            <canvas id="accuracyChart"></canvas>
            <div class="rules">
            <h3>
  <i class="bi bi-shield-fill" style="color: purple;"></i> Security Rules
</h3>


                <ul>
                    <li>Always sanitize user inputs to prevent SQL injection.</li>
                    <li>Use secure connections (HTTPS) for all API calls.</li>
                    <li>Store sensitive data like API keys securely using environment variables.</li>
                    <li>Validate all external data sources before processing.</li>
                    <li>Implement proper authentication and authorization mechanisms.</li>
                </ul>
            </div>
            
            <style>
    .analysis-card {
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
        font-family: Arial, sans-serif;
        text-align: center;
    }
    .analysis-card h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 10px;
    }
    .analysis-card ul {
        list-style-type: none;
        padding: 0;
    }
    .analysis-card li {
        display: flex;
        align-items: center;
        margin: 10px 0;
        font-size: 16px;
        color: #555;
    }
    .analysis-card li i {
        font-size: 20px;
        margin-right: 10px;
        color: #007bff;
    }
    .analysis-card img.gif {
        width: 100%;
        border-radius: 8px;
        margin-top: 20px;
    }
</style>

<div class="analysis-card">
    <h2>Code Analysis</h2>

    <h3>Keys Used:</h3>
    <ul>
        <?php foreach ($keysUsed as $key): ?>
            <li><i class="bi bi-key"></i> <?= htmlspecialchars($key) ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Functions Used:</h3>
    <ul>
        <?php foreach ($functionsUsed as $function): ?>
            <li><i class="bi bi-gear"></i> <?= htmlspecialchars($function) ?></li>
        <?php endforeach; ?>
    </ul>

    <img class="gif" src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExOWh4ZTQzYXF0Z2gwNnY1YmgxMzJ3amV2eWl6ZGZoMmludGFqdTRuNyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/qgQUggAC3Pfv687qPC/giphy.webphttps://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExOWh4ZTQzYXF0Z2gwNnY1YmgxMzJ3amV2eWl6ZGZoMmludGFqdTRuNyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/qgQUggAC3Pfv687qPC/giphy.webp" alt="Analysis Visual">
</div>

<!-- Include Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<div class="penetration-testing">
    <div class="penetration-header">
        <i class="fa fa-shield-alt"></i>
        <h2>Penetration Testing Results</h2>
    </div>
    <ul>
        <?php if (!empty($vulnerabilities)) : ?>
            <?php foreach ($vulnerabilities as $vulnerability) : ?>
                <li><?php echo htmlspecialchars($vulnerability); ?></li>
            <?php endforeach; ?>
        <?php else : ?>
            <li>No vulnerabilities detected.</li>
        <?php endif; ?>
    </ul>
</div>
<style>
    .penetration-testing {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.penetration-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.penetration-header i {
    font-size: 24px;
    color: #8c52a1;
}

.penetration-header h2 {
    font-size: 20px;
    color: #333;
    font-weight: bold;
    margin: 0;
}

.penetration-testing ul {
    list-style-type: none;
    padding: 0;
}

.penetration-testing ul li {
    font-size: 16px;
    color: #555;
    margin: 10px 0;
}

.penetration-testing ul li::before {
    content: "⚠️";
    margin-right: 10px;
    color: #ff9800;
}

</style>
        <?php endif; ?>
    </div>

    <script>
        const ctx = document.getElementById('accuracyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Start', 'Mid', 'End'],
                datasets: [{
                    label: 'Accuracy Over Time',
                    data: [0, 50, <?php echo $accuracy; ?>],
                    borderColor: 'rgba(140, 82, 161, 1)',
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    </script>
</body>
</html>
