class MealModel {
  final String id;
  final String restaurantId; // 👈 جديد
  final String name;
  final String description;
  final double price;
  final String image;

  MealModel({
    required this.id,
    required this.restaurantId,
    required this.name,
    required this.description,
    required this.price,
    required this.image,
  });
}
