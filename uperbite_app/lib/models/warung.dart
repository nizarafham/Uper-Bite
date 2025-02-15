import 'menu.dart';
import 'penjual.dart';

class Warung {
  final int id;
  final String nama;
  final Penjual penjual;
  final List<Menu> menus;

  Warung({
    required this.id,
    required this.nama,
    required this.penjual,
    required this.menus,
  });

  factory Warung.fromJson(Map<String, dynamic> json) {
    return Warung(
      id: json['id'] ?? 0, // Berikan nilai default jika null
      nama: json['nama'] ?? 'Nama tidak tersedia', // Berikan nilai default jika null
      penjual: Penjual.fromJson(json['penjual']),
      menus: (json['menus'] as List? ?? []).map((menu) => Menu.fromJson(menu)).toList(),
    );
  }
}