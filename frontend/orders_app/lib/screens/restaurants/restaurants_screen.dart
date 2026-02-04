import 'package:flutter/material.dart';
import '../../models/restaurant_model.dart';
import '../home/home_screen.dart';

class RestaurantsScreen extends StatelessWidget {
  const RestaurantsScreen({super.key});

  static final List<RestaurantModel> restaurants = [
    RestaurantModel(
      id: '1',
      name: 'Burger House',
      image:
      'https://images.unsplash.com/photo-1550547660-d9450f859349',
    ),
    RestaurantModel(
      id: '2',
      name: 'Italian Kitchen',
      image:
      'https://images.unsplash.com/photo-1606755962773-d324e0a13086',
    ),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Restaurants'),
        centerTitle: true,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: restaurants.length,
        itemBuilder: (context, index) {
          final restaurant = restaurants[index];

          return Card(
            margin: const EdgeInsets.only(bottom: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
            ),
            child: ListTile(
              contentPadding: const EdgeInsets.all(12),
              leading: ClipRRect(
                borderRadius: BorderRadius.circular(12),
                child: Image.network(
                  restaurant.image,
                  width: 60,
                  height: 60,
                  fit: BoxFit.cover,
                ),
              ),
              title: Text(
                restaurant.name,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                ),
              ),
              trailing: const Icon(Icons.arrow_forward_ios, size: 16),
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => HomeScreen(
                      restaurantId: restaurant.id,
                      restaurantName: restaurant.name,
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
