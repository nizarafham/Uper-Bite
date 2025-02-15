import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../home/home_Page.dart';
import '../home/warung_page.dart';

class LoginPage extends StatefulWidget {
  @override
  _LoginPageState createState() => _LoginPageState();
}

  class _LoginPageState extends State<LoginPage> {
  final TextEditingController nimController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  bool isLoading = false;

  Future<void> login() async {
    if (isLoading) return; // Cegah double request
    setState(() => isLoading = true);

    final response = await http.post(
      Uri.parse('http://localhost:8000/api/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'identifier': nimController.text,
        'password': passwordController.text,
      }),
    );

    print("Response Status Code: ${response.statusCode}");
    print("Response Body: ${response.body}");

    final responseData = jsonDecode(response.body);
    setState(() => isLoading = false);

    if (response.statusCode == 200) {
      // Simpan token untuk sesi login
      String token = responseData['token'];
      String role = responseData['role'];
      
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text("Login berhasil!")));
      if (role == 'mahasiswa' || role == 'dosen') {
        Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => HomePage(identifier: nimController.text, token: token),
        ),
      );
      } else if (role == 'penjual') {
      int warungId = responseData['warung_id'] ?? 0;  // Ambil warung_id
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => WarungPage(warungId: warungId, token: token), // Gunakan warungId
        ),
      );
    }

    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(responseData['message'] ?? 'Error')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Login')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(controller: nimController, decoration: InputDecoration(labelText: "NIM")),
            TextField(controller: passwordController, obscureText: true, decoration: InputDecoration(labelText: "Password")),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: isLoading ? null : login, // Cegah klik dua kali saat loading
              child: isLoading ? CircularProgressIndicator() : Text('Login'),
            )
          ],
        ),
      ),
    );
  }
}
