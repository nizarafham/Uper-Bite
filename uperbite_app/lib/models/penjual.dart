class Penjual {
  final int id;
  final String identifier;
  final String email;
  final String role;
  final int emailVerified;
  final String createdAt;
  final String updatedAt;

  Penjual({
    required this.id,
    required this.identifier,
    required this.email,
    required this.role,
    required this.emailVerified,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Penjual.fromJson(Map<String, dynamic> json) {
    return Penjual(
      id: json['id'],
      identifier: json['identifier'],
      email: json['email'],
      role: json['role'],
      emailVerified: json['email_verified'],
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }
}