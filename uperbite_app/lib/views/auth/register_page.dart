import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'verify_token_page.dart';

class RegisterPage extends StatefulWidget {
  @override
  _RegisterPageState createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final TextEditingController nimController = TextEditingController();
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  bool isLoading = false;

  Future<void> register() async {
    setState(() => isLoading = true);

    final response = await http.post(
      Uri.parse('http://localhost:8000/api/register'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'nim': nimController.text,
        'email': emailController.text,
        'password': passwordController.text,
      }),
    );

    final responseData = jsonDecode(response.body);
    setState(() => isLoading = false);

    if (response.statusCode == 200) {
      // Berhasil daftar, minta user memasukkan token verifikasi
      Navigator.push(
        context,
        MaterialPageRoute(builder: (context) => VerifyTokenPage(email: emailController.text)),
      );
    } else {
      // Tampilkan error
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(responseData['message'] ?? 'Error')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Register')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(controller: nimController, decoration: InputDecoration(labelText: "NIM")),
            TextField(controller: emailController, decoration: InputDecoration(labelText: "Email")),
            TextField(controller: passwordController, obscureText: true, decoration: InputDecoration(labelText: "Password")),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: isLoading ? null : register,
              child: isLoading ? CircularProgressIndicator() : Text('Register'),
            ),
          ],
        ),
      ),
    );
  }
}
