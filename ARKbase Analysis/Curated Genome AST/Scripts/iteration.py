import os
import subprocess
import shutil
from pathlib import Path

def run_panaroo(gff_files, test_dir):
    """Runs Panaroo on a subset of GFF files in a temporary directory."""
    test_dir = Path(test_dir).resolve()
    os.makedirs(test_dir, exist_ok=True)

    # Copy GFF files to the test directory
    for gff in gff_files:
        shutil.copy(gff, test_dir)

    try:
        gff_paths = [str(p.name) for p in Path(test_dir).glob("*.gff")]  # just filenames
        output_dir = test_dir / "panaroo_out"

        # Make sure output directory path is valid
        output_dir.parent.mkdir(parents=True, exist_ok=True)

        # Run Panaroo from inside the test directory
        subprocess.run(
            ["panaroo", "-i"] + gff_paths + ["--clean-mode", "strict", "-o", str(output_dir)],
            cwd=test_dir,
            check=True
        )
        return True

    except subprocess.CalledProcessError:
        return False

    finally:
        shutil.rmtree(test_dir)  # Always clean up the temporary test directory

def recursive_test(gff_files, depth=0):
    """Recursively test GFF files in halves to isolate those that break Panaroo."""
    if len(gff_files) == 1:
        bad_file = gff_files[0]
        print(f"❌ Problematic file: {bad_file}")
        with open("problematic_files.log", "a") as log:
            log.write(bad_file + "\n")
        return

    mid = len(gff_files) // 2
    left = gff_files[:mid]
    right = gff_files[mid:]

    test_id = f"depth_{depth}"

    print(f"\n🧪 Testing left half ({len(left)} files)...")
    if not run_panaroo(left, f"temp_run_{test_id}_left"):
        recursive_test(left, depth + 1)
    else:
        print("✅ Left half passed.")

    print(f"\n🧪 Testing right half ({len(right)} files)...")
    if not run_panaroo(right, f"temp_run_{test_id}_right"):
        recursive_test(right, depth + 1)
    else:
        print("✅ Right half passed.")

if __name__ == "__main__":
    # Replace this with the actual path to your GFF files
    gff_dir = "/home/dell/ark_base/Enterococcus_faecium/gff_files"

    # Find all .gff files in the directory (absolute paths)
    all_gffs = sorted([str(p.resolve()) for p in Path(gff_dir).glob("*.gff")])

    print(f"🔍 Total GFF files found: {len(all_gffs)}")
    print("🚀 Starting recursive testing...\n")
    recursive_test(all_gffs)

