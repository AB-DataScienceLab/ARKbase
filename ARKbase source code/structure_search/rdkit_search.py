import csv
import json
import argparse
import sys
import io
import base64
from rdkit import Chem
from rdkit.Chem import Draw, rdFingerprintGenerator
from rdkit.DataStructs import TanimotoSimilarity

def search_and_generate(query_smiles, threshold, db_path):
    fpgen = rdFingerprintGenerator.GetMorganGenerator(radius=2, fpSize=1024)

    query_mol = Chem.MolFromSmiles(query_smiles.strip())
    if query_mol is None:
        # Fixed garbled text
        print("❌ Error: Query SMILES could not be parsed.", file=sys.stderr)
        return []

    query_fp = fpgen.GetFingerprint(query_mol)
    found_molecules = []

    try:
        with open(db_path, mode='r', encoding='utf-8-sig') as csvfile:
            reader = csv.DictReader(csvfile)
            for row in reader:
                db_smiles = row.get('SMILES', '').strip()
                drug_name = row.get('DRUG NAME', '').strip()
                pathogen = row.get('pathogen_id', 'N/A').strip()

                if not db_smiles or not drug_name:
                    continue

                db_mol = Chem.MolFromSmiles(db_smiles)
                if db_mol is None:
                    continue

                db_fp = fpgen.GetFingerprint(db_mol)
                similarity = TanimotoSimilarity(query_fp, db_fp)

                if similarity >= threshold:
                    img = Draw.MolToImage(db_mol, size=(300, 300))
                    buffer = io.BytesIO()
                    img.save(buffer, format='PNG')
                    base64_img = base64.b64encode(buffer.getvalue()).decode('utf-8')
                    
                    found_molecules.append({
                        "drug_name": drug_name,
                        "pathogen": pathogen,
                        "smiles": db_smiles,
                        "similarity": similarity,
                        "image_base64": base64_img
                    })
    
    except Exception as e:
        # Fixed garbled text
        print(f"❌ Error reading the database file: {e}", file=sys.stderr)
        return []

    if not found_molecules:
        # Fixed garbled text
        print("⚠️ No structures found above the threshold.", file=sys.stderr)
        return []

    # --- THIS IS THE NEW SORTING LOGIC ---
    # Sort the list of dictionaries by the 'similarity' value in descending order.
    sorted_molecules = sorted(found_molecules, key=lambda item: item['similarity'], reverse=True)

    return sorted_molecules

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="RDKit Similarity Search")
    parser.add_argument("--query", required=True, help="Query SMILES")
    parser.add_argument("--db_file", required=True, help="Path to drug CSV")
    parser.add_argument("--threshold", type=float, default=0.8, help="Tanimoto threshold")

    args = parser.parse_args()
    results = search_and_generate(args.query, args.threshold, args.db_file)

    if results:
        # This will now print the already sorted list of results
        print(json.dumps(results, indent=2))