class OrderModel {
  final int id;
  final String status;
  final double total;
  final String paymentMethod;
  final String createdAt;

  OrderModel({
    required this.id,
    required this.status,
    required this.total,
    required this.paymentMethod,
    required this.createdAt,
  });

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    return OrderModel(
      id: json['id'],
      status: json['status'],
      total: double.parse(json['total'].toString()),
      paymentMethod: json['payment_method'],
      createdAt: json['created_at'],
    );
  }
}
