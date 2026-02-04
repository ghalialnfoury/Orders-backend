import 'package:flutter/material.dart';

class OrderCard extends StatelessWidget {
  final int orderId;

  const OrderCard({super.key, required this.orderId});

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: ListTile(
        leading: const Icon(Icons.shopping_cart),
        title: Text('Order #$orderId'),
        subtitle: const Text('Status: Pending'),
        trailing: const Icon(Icons.arrow_forward_ios, size: 16),
      ),
    );
  }
}
