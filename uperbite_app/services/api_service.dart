import 'package:dio/dio.dart';

class ApiService {
  static final Dio dio = Dio(
    BaseOptions(
      baseUrl: "http://localhost:8000/api", // Ganti dengan URL backend Laravel
      connectTimeout: Duration(seconds: 10),
      receiveTimeout: Duration(seconds: 10),
    ),
  );

  static Future<Response> getData(String endpoint) async {
    try {
      return await dio.get(endpoint);
    } catch (e) {
      rethrow;
    }
  }
}
