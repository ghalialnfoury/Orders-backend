import 'package:flutter/material.dart';
import '../models/cart_item.dart';
import '../models/meal_model.dart';

class CartProvider extends ChangeNotifier {
  final List<CartItem> _items = [];

  List<CartItem> get items => _items;

  void addMeal(MealModel meal) {
    final index = _items.indexWhere((item) => item.meal.id == meal.id);

    if (index >= 0) {
      _items[index].quantity++;
    } else {
      _items.add(CartItem(meal: meal));
    }
    notifyListeners();
  }

  double get totalPrice {
    double total = 0;
    for (var item in _items) {
      total += item.meal.price * item.quantity;
    }
    return total;
  }
}
