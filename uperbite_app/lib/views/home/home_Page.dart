import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../../models/warung.dart';
import '../../models/menu.dart';

class HomePage extends StatefulWidget {
  final String identifier;
  final String token;

  HomePage({required this.identifier, required this.token});

  @override
  _HomePageState createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  List<Warung> warungs = []; // Untuk menyimpan data warung
  List<Menu> menus = []; // Untuk menyimpan data menu
  bool isLoading = true; // Loading state untuk warung
  bool isMenuLoading = true; // Loading state untuk menu

  @override
  void initState() {
    super.initState();
    fetchWarungData(); // Ambil data warung
    fetchMenuData(); // Ambil data menu
  }

  // Fungsi untuk mengambil data warung dari API
  Future<void> fetchWarungData() async {
  try {
    final response = await http.get(
      Uri.parse('http://localhost:8000/api/warungs'),
      headers: {
        'Authorization': 'Bearer ${widget.token}',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      if (data['warungs'] != null) {
        setState(() {
          warungs = List<Warung>.from(
            (data['warungs'] as List).map((warungData) {
              // Debugging: Cetak data warung
              print(warungData);
              return Warung.fromJson(warungData);
            }),
          );
        });
      } else {
        print('Data warungs tidak ditemukan');
      }
      isLoading = false;
    } else {
      print('Gagal mengambil data warung: ${response.statusCode}');
      print('Response body: ${response.body}');
      setState(() => isLoading = false);
    }
  } catch (e) {
    print('Error mengambil data warung: $e');
    setState(() => isLoading = false);
  }
}

  // Fungsi untuk mengambil data menu dari API
  Future<void> fetchMenuData() async {
  try {
    final response = await http.get(
      Uri.parse('http://localhost:8000/api/menus'),
      headers: {
        'Authorization': 'Bearer ${widget.token}',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final List<dynamic> data = json.decode(response.body);
      setState(() {
        menus = data.map((menuData) => Menu.fromJson(menuData)).toList();
        isMenuLoading = false;
      });
    } else {
      print('Gagal mengambil data menu: ${response.statusCode}');
      print('Response body: ${response.body}');
      setState(() => isMenuLoading = false);
    }
  } catch (e) {
    print('Error mengambil data menu: $e');
    setState(() => isMenuLoading = false);
  }
}

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            Expanded(
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(5),
                  border: Border.all(color: Colors.grey),
                ),
                child: TextField(
                  decoration: InputDecoration(
                    hintText: 'Cari nama makanan...',
                    border: InputBorder.none,
                    contentPadding: EdgeInsets.symmetric(horizontal: 10),
                  ),
                ),
              ),
            ),
            SizedBox(width: 10),
            IconButton(
              icon: Icon(Icons.person),
              onPressed: () {
                Navigator.pushNamed(context, '/profile');
              },
            ),
          ],
        ),
      ),
      body: isLoading || isMenuLoading
          ? Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              child: Column(
                children: [
                  Image.network(
                    'https://via.placeholder.com/400x200',
                    fit: BoxFit.cover,
                  ),
                  SizedBox(height: 10),
                  _buildWarungGrid(), // Tampilkan grid warung
                  SizedBox(height: 10),
                  _buildMenuList(), // Tampilkan list menu
                ],
              ),
            ),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: const <BottomNavigationBarItem>[
          BottomNavigationBarItem(
            icon: Icon(Icons.home),
            label: 'Beranda',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.local_offer),
            label: 'Promo',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.history),
            label: 'History',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.chat),
            label: 'Chat',
          ),
        ],
      ),
    );
  }

  // Widget untuk menampilkan grid warung
  Widget _buildWarungGrid() {
    return Container(
      padding: EdgeInsets.all(10),
      child: GridView.builder(
        shrinkWrap: true,
        physics: NeverScrollableScrollPhysics(),
        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 3,
          crossAxisSpacing: 10,
          mainAxisSpacing: 10,
        ),
        itemCount: warungs.length,
        itemBuilder: (context, index) {
          final warung = warungs[index];
          return Container(
            decoration: BoxDecoration(
              color: Colors.blueAccent,
              borderRadius: BorderRadius.circular(5),
            ),
            child: Center(
              child: Text(
                warung.nama,
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
              ),
            ),
          );
        },
      ),
    );
  }

  // Widget untuk menampilkan list menu
  Widget _buildMenuList() {
    return Container(
      padding: EdgeInsets.all(10),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Daftar Menu',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
          SizedBox(height: 10),
          ListView.builder(
            shrinkWrap: true,
            physics: NeverScrollableScrollPhysics(),
            itemCount: menus.length,
            itemBuilder: (context, index) {
              final menu = menus[index];
              return Card(
                margin: EdgeInsets.only(bottom: 10),
                child: ListTile(
                  title: Text(menu.nama),
                  subtitle: Text('Rp ${menu.harga} - ${menu.kategori}'),
                  trailing: Text(menu.warung.nama),
                ),
              );
            },
          ),
        ],
      ),
    );
  }
}