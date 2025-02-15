import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class VerifyTokenPage extends StatefulWidget {
  final String email;

  VerifyTokenPage({required this.email});

  @override
  _VerifyTokenPageState createState() => _VerifyTokenPageState();
}

class _VerifyTokenPageState extends State<VerifyTokenPage> {
  final TextEditingController tokenController = TextEditingController();
  bool isLoading = false;

  Future<void> verifyToken() async {
    setState(() => isLoading = true);

    final response = await http.post(
      Uri.parse('http://localhost:8000/api/verify-email'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': widget.email,
        'token': tokenController.text,
      }),
    );

    final responseData = jsonDecode(response.body);
    setState(() => isLoading = false);

    if (response.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text("Email berhasil diverifikasi! Silakan login.")));
      Navigator.pop(context); // Kembali ke login
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(responseData['message'] ?? 'Error')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Verifikasi Email')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(controller: tokenController, decoration: InputDecoration(labelText: "Masukkan Token")),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: isLoading ? null : verifyToken,
              child: isLoading ? CircularProgressIndicator() : Text('Verifikasi'),
            ),
          ],
        ),
      ),
    );
  }
}
