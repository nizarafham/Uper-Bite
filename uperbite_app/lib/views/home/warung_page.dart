import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class WarungPage extends StatefulWidget {
  final int warungId; // Gunakan ID, bukan identifier
  final String token;

  const WarungPage({super.key, required this.warungId, required this.token});

  @override
  _WarungPageState createState() => _WarungPageState();
}

class _WarungPageState extends State<WarungPage> {
  Map<String, dynamic>? warung;
  List<dynamic> menus = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    fetchWarungData();
  }

  Future<void> fetchWarungData() async {
    try {
      final response = await http.get(
        Uri.parse('http://localhost:8000/api/warungs/${widget.warungId}'),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        setState(() {
          warung = data['warung'] ?? {};
          menus = data['warung']?['menus'] ?? []; // Ambil dari warung
          isLoading = false;
        });
      } else {
        print('Gagal mengambil data warung: ${response.statusCode}');
        setState(() => isLoading = false);
      }
    } catch (e) {
      print('Error mengambil data warung: $e');
      setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          warung?['nama']?.toString() ?? 'Warung',
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator())
          : warung == null
              ? const Center(child: Text('Data warung tidak ditemukan'))
              : Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Padding(
                      padding: const EdgeInsets.all(12.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Nama Warung: ${warung?['nama'] ?? 'Tidak diketahui'}',
                            style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                          ),
                          Text(
                            'Lokasi: ${warung?['lokasi'] ?? 'Tidak diketahui'}',
                            style: const TextStyle(fontSize: 16),
                          ),
                        ],
                      ),
                    ),
                    ElevatedButton(
                      onPressed: () {
                        // Navigasi ke halaman tambah menu
                      },
                      child: const Text('Tambah Menu'),
                    ),
                    const SizedBox(height: 10),
                    Expanded(
                      child: menus.isEmpty
                          ? const Center(child: Text('Belum ada menu'))
                          : ListView.builder(
                              itemCount: menus.length,
                              itemBuilder: (context, index) {
                                final menu = menus[index];
                                return Card(
                                  elevation: 2,
                                  margin: const EdgeInsets.symmetric(
                                      horizontal: 12, vertical: 6),
                                  child: ListTile(
                                    title: Text(
                                    menu['nama']?.toString() ?? 'Tanpa Nama', // Ubah dari nama_menu
                                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                                  ),
                                  subtitle: Text(
                                    'Harga: Rp${menu['harga']?.toString() ?? 'N/A'}',
                                    style: const TextStyle(fontSize: 14),
                                  ),

                                  ),
                                );
                              },
                            ),
                    ),
                  ],
                ),
    );
  }
}
