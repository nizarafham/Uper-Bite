import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class AuthProvider with ChangeNotifier {
  String? _token;
  String? get token => _token;

  Future<String?> login(String nim, String password) async {
    try {
      final response = await http.post(
        Uri.parse('http://localhost:8000/api/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'nim': nim, 'password': password}),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        _token = data['token']; // Simpan token JWT
        notifyListeners();
        return null;
      } else {
        final data = jsonDecode(response.body);
        return data['message'];
      }
    } catch (e) {
      return 'Gagal terhubung ke server';
    }
  }

  void logout() {
    _token = null;
    notifyListeners();
  }
}
