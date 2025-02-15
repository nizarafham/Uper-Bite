import '../models/warung.dart';

class Menu {
  final int id;
  final int warungId;
  final String nama;
  final String harga;
  final String kategori;
  final Warung warung;

  Menu({
    required this.id,
    required this.warungId,
    required this.nama,
    required this.harga,
    required this.kategori,
    required this.warung,
  });

  factory Menu.fromJson(Map<String, dynamic> json) {
    return Menu(
      id: json['id'] ?? 0,
      warungId: json['warung_id'] ?? 0,
      nama: json['nama'] ?? 'Menu tidak tersedia',
      harga: json['harga'] ?? '0.00',
      kategori: json['kategori'] ?? 'Kategori tidak tersedia',
      warung: Warung.fromJson(json['warung']),
    );
  }
}