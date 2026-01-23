import 'package:flutter/material.dart';
import '../../widgets/custom_button.dart';
import '../../widgets/custom_text_field.dart';

class RegisterScreen extends StatelessWidget {
  RegisterScreen({super.key});

  final TextEditingController nameController = TextEditingController();
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Create Account'),
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: SingleChildScrollView(
          child: Column(
            children: [
              const SizedBox(height: 40),

              const Text(
                'Register',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                ),
              ),

              const SizedBox(height: 30),

              CustomTextField(
                hint: 'Full Name',
                icon: Icons.person,
                controller: nameController,
              ),
              const SizedBox(height: 16),

              CustomTextField(
                hint: 'Email',
                icon: Icons.email,
                controller: emailController,
              ),
              const SizedBox(height: 16),

              CustomTextField(
                hint: 'Password',
                icon: Icons.lock,
                isPassword: true,
                controller: passwordController,
              ),

               SizedBox(height: 30),

              CustomButton(
                text: 'Create Account',
                onPressed: () {
                  // لاحقاً: API / Validation
                  Navigator.pop(context);
                },
              ),

              const SizedBox(height: 20),

              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text('Already have an account? '),
                  GestureDetector(
                    onTap: () {
                      Navigator.pop(context);
                    },
                    child: const Text(
                      'Login',
                      style: TextStyle(
                        color: Colors.blue,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
