<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .register-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        .register-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .register-container input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .register-container button:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form id="registerForm" action="/api/register" method="POST">
            @csrf
            <div>
                <label for="identifier">Identifier:</label>
                <input type="text" id="identifier" name="identifier" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="penjual">Penjual</option>
                </select>
            </div>
            <button type="submit">Register</button>
        </form>
        <div id="errorMessages" class="error-message"></div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const response = await fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData
            });

            const result = await response.json();

            if (response.status === 201) {
                alert(result.message);
                window.location.href = '/login'; // Redirect ke halaman login setelah registrasi berhasil
            } else {
                let errorHtml = '';
                for (const key in result.errors) {
                    errorHtml += `<p>${result.errors[key]}</p>`;
                }
                document.getElementById('errorMessages').innerHTML = errorHtml;
            }
        });
    </script>
</body>
</html>
