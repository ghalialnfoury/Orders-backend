import 'meal_model.dart';

class CartItem {
  final MealModel meal;
  int quantity;

  CartItem({
    required this.meal,
    this.quantity = 1,
  });
}
