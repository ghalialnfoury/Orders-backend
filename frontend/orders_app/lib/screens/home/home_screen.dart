import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/meal_model.dart';
import '../../providers/cart_provider.dart';

class HomeScreen extends StatelessWidget {
  final String restaurantId;
  final String restaurantName;

  const HomeScreen({
    super.key,
    required this.restaurantId,
    required this.restaurantName,
  });

  /// Fake Meals (لاحقًا من API)
  static final List<MealModel> meals = [
    MealModel(
      id: '1',
      restaurantId: '1',
      name: 'Cheese Burger',
      description: 'Juicy beef burger with melted cheese',
      price: 8.99,
      image:
      'https://images.unsplash.com/photo-1550547660-d9450f859349',
    ),
    MealModel(
      id: '2',
      restaurantId: '1',
      name: 'Double Burger',
      description: 'Double beef with cheddar',
      price: 11.50,
      image:
      'https://images.unsplash.com/photo-1550547660-d9450f859349',
    ),
    MealModel(
      id: '3',
      restaurantId: '2',
      name: 'Pizza',
      description: 'Italian pizza with mozzarella cheese',
      price: 12.50,
      image:
      'https://images.unsplash.com/photo-1606755962773-d324e0a13086',
    ),
    MealModel(
      id: '4',
      restaurantId: '2',
      name: 'Pasta',
      description: 'Creamy chicken pasta',
      price: 10.00,
      image:
      'https://images.unsplash.com/photo-1525755662778-989d0524087e',
    ),
  ];

  @override
  Widget build(BuildContext context) {
    /// فلترة الوجبات حسب المطعم
    final restaurantMeals =
    meals.where((m) => m.restaurantId == restaurantId).toList();

    return Scaffold(
      appBar: AppBar(
        title: Text(restaurantName),
        centerTitle: true,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: restaurantMeals.length,
        itemBuilder: (context, index) {
          final meal = restaurantMeals[index];

          return Card(
            margin: const EdgeInsets.only(bottom: 16),
            elevation: 4,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
            ),
            child: SizedBox(
              height: 130, // ✅ يمنع أي overflow
              child: Row(
                children: [
                  /// Image
                  ClipRRect(
                    borderRadius: const BorderRadius.horizontal(
                      left: Radius.circular(16),
                    ),
                    child: Image.network(
                      meal.image,
                      width: 120,
                      height: 130,
                      fit: BoxFit.cover,
                      loadingBuilder: (context, child, loadingProgress) {
                        if (loadingProgress == null) return child;
                        return const SizedBox(
                          width: 120,
                          height: 130,
                          child: Center(
                            child: CircularProgressIndicator(strokeWidth: 2),
                          ),
                        );
                      },
                      errorBuilder: (_, __, ___) {
                        return Container(
                          width: 120,
                          height: 130,
                          color: Colors.grey.shade300,
                          child: const Icon(
                            Icons.fastfood,
                            size: 40,
                            color: Colors.grey,
                          ),
                        );
                      },
                    ),
                  ),

                  /// Info
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                meal.name,
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(
                                  fontSize: 17,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                meal.description,
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(
                                  fontSize: 13,
                                  color: Colors.grey,
                                ),
                              ),
                            ],
                          ),

                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                '\$${meal.price.toStringAsFixed(2)}',
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              ElevatedButton(
                                onPressed: () {
                                  context
                                      .read<CartProvider>()
                                      .addMeal(meal);

                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text('Added to order'),
                                      duration: Duration(seconds: 1),
                                    ),
                                  );
                                },
                                child: const Text(
                                  'Add',
                                  style: TextStyle(fontSize: 13),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
