from flask import Flask, request, jsonify

app = Flask(__name__)

# Home route
@app.route('/')
def home():
    return jsonify({"message": "Welcome to the Code Review API"})

# Review route - this handles POST requests to analyze the code
@app.route('/review', methods=['POST'])
def review():
    try:
        # Get the JSON data from the request
        data = request.get_json()

        # Check if 'code' key exists in the payload
        code = data.get('code')
        language = data.get('language')

        # If code is not provided, return an error
        if not code:
            return jsonify({"error": "No code provided"}), 400
        
        # Dummy analysis function - This is where you would implement real code review logic
        # For now, let's pretend we found some basic issues in the code.
        errors = []
        warnings = []
        suggestions = []

        # Example logic (you can replace with an actual analysis model)
        if "exec(" in code:
            errors.append("Unsafe 'exec()' function detected. Avoid using it.")
        
        if len(code) > 500:  # Warning for long code
            warnings.append("Code size is large. Consider optimizing it.")
        
        suggestions.append("Consider using functions to organize code better.")

        # Return the analysis results
        return jsonify({
            "errors": errors,
            "warnings": warnings,
            "suggestions": suggestions
        })

    except Exception as e:
        # Return an error if anything goes wrong
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
