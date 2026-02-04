import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';

class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();

    return Scaffold(
      appBar: AppBar(
        title: const Text('Your Order'),
        centerTitle: true,
      ),
      body: cart.items.isEmpty
          ? const Center(child: Text('Cart is empty'))
          : Column(
        children: [
          Expanded(
            child: ListView.builder(
              itemCount: cart.items.length,
              itemBuilder: (context, index) {
                final item = cart.items[index];

                return ListTile(
                  leading: Image.network(
                    item.meal.image,
                    width: 50,
                    fit: BoxFit.cover,
                  ),
                  title: Text(item.meal.name),
                  subtitle: Text(
                    'Qty: ${item.quantity} • \$${item.meal.price}',
                  ),
                  trailing: Text(
                    '\$${(item.meal.price * item.quantity).toStringAsFixed(2)}',
                  ),
                );
              },
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Text(
              'Total: \$${cart.totalPrice.toStringAsFixed(2)}',
              style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
