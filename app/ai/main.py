#!/usr/bin/python
import sys
import google.generativeai as genai

def generate_recipe(ingredients, api_key):
    genai.configure(api_key=api_key)
    model = genai.GenerativeModel('gemini-pro')

    # Enforce a structured recipe prompt
    prompt = f"""Generate a recipe using only the following ingredients with an Indian touch : {', '.join(ingredients)}.
    The recipe must follow this format:
    Title: [Title of the dish]
    Description: [A short description of the dish]
    Ingredients: 
    - [Ingredient 1]
    - [Ingredient 2]
    Steps: 
    1. [Step 1]
    2. [Step 2]
    Servings: [Number of servings]
    Tips: 
    - [Tip 1]
    - [Tip 2]
    """

    try:
        response = model.generate_content(prompt)
        if response and hasattr(response, 'text'):
            return response.text
        else:
            return "Error generating recipe: No valid response from the AI model."
    except Exception as e:
        return f"Error generating recipe: {str(e)}"

if __name__ == "__main__":
    api_key = '# Your API key'

    if len(sys.argv) > 1:
        ingredients = sys.argv[1].split(',')  # Get ingredients from command line args

        # Generate the recipe using AI
        recipe = generate_recipe(ingredients, api_key)
       
        # Output the recipe in plain text
        print("Content-type: text/html\n")
        print(recipe)
    else:
        print("Content-type: text/html\n")
        print("Please provide ingredients to generate a recipe.")
