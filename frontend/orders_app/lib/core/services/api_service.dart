import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static const baseUrl = 'http://YOUR_API_URL/api';

  static Future<List<dynamic>> getOrders() async {
    final response = await http.get(Uri.parse('$baseUrl/orders'));
    return jsonDecode(response.body);
  }
}
