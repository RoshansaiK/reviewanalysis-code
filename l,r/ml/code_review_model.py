from flask import Flask, request, jsonify
import pickle 

app = Flask(__name__)

def analyze_code_with_model(code):
   
    errors = []
    warnings = []
    suggestions = []

    # Dummy error detection (for illustration)
    if "exec(" in code:
        errors.append("Unsafe 'exec()' function detected. Avoid using it.")
    
    # Warnings
    if len(code) > 1000:  # Example: Large code might have performance issues
        warnings.append("Code size is large. Consider optimizing it.")

    # Suggestions
    suggestions.append("Consider using functions to organize code better.")

    # Return the results as a dictionary
    return {"errors": errors, "warnings": warnings, "suggestions": suggestions}

@app.route('/review', methods=['POST'])
def review_code():
    try:
        # Get the posted JSON data
        data = request.get_json()

        # Extract the code input from the JSON
        code = data.get('code')
        language = data.get('language')

        if not code:
            return jsonify({"error": "No code provided"}), 400

        # Analyze the code using the model (dummy analysis in this case)
        model_results = analyze_code_with_model(code)

        return jsonify({
            "errors": model_results['errors'],
            "warnings": model_results['warnings'],
            "suggestions": model_results['suggestions']
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
