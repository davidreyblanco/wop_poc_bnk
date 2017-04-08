#!/bin/bash
php generate_sample_suggestion.php >data/suggestion_dummy.txt
php csv2json.php data/suggestion_dummy.txt >payload/suggestions.json
php generate_sample_pipeline.php >payload/pipeline.json
