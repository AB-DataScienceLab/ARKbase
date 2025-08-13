import pandas as pd
import networkx as nx
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
import os
from collections import Counter

# Define the input directory containing the CSV files
input_dir = "/home/shwdey/check/"
output_dir = "/home/shwdey/check/v"

# Ensure the output directory exists
os.makedirs(output_dir, exist_ok=True)

# Get all CSV files from the input directory
files = [f for f in os.listdir(input_dir) if f.endswith("_filtered.csv")]

def calculate_network_stats(G):
    stats = {
        'nodes': G.number_of_nodes(),
        'edges': G.number_of_edges(),
        'density': nx.density(G),
        'avg_clustering': nx.average_clustering(G),
        'avg_degree': sum(dict(G.degree()).values()) / G.number_of_nodes() if G.number_of_nodes() > 0 else 0,
        'diameter': nx.diameter(G) if nx.is_connected(G) else 'N/A (disconnected)',
        'avg_path_length': nx.average_shortest_path_length(G) if nx.is_connected(G) else 'N/A (disconnected)',
        'num_components': nx.number_connected_components(G),
        'largest_component_size': len(max(nx.connected_components(G), key=len)) if G.number_of_nodes() > 0 else 0
    }
    return stats

def assign_quartiles(values):
    quartiles = np.percentile(values, [25, 50, 75])
    labels = []
    for val in values:
        if val >= quartiles[2]:
            labels.append('Q1')
        elif val >= quartiles[1]:
            labels.append('Q2')
        elif val >= quartiles[0]:
            labels.append('Q3')
        else:
            labels.append('Q4')
    return labels, quartiles

def plot_degree_distribution(G, file_name, output_path):
    degrees = [G.degree(n) for n in G.nodes()]
    degree_counts = Counter(degrees)

    plt.figure(figsize=(15, 5))

    plt.subplot(1, 3, 1)
    plt.hist(degrees, bins=50, alpha=0.7, color='skyblue', edgecolor='black')
    plt.xlabel('Degree')
    plt.ylabel('Frequency')
    plt.title(f'Degree Distribution - {file_name}')
    plt.grid(True, alpha=0.3)

    plt.subplot(1, 3, 2)
    degrees_sorted = sorted(degree_counts.keys())
    counts = [degree_counts[d] for d in degrees_sorted]
    plt.loglog(degrees_sorted, counts, 'bo-', alpha=0.7)
    plt.xlabel('Degree (log scale)')
    plt.ylabel('Count (log scale)')
    plt.title('Degree Distribution (Log-Log)')
    plt.grid(True, alpha=0.3)

    plt.subplot(1, 3, 3)
    degrees_sorted = sorted(degrees, reverse=True)
    cumulative = np.arange(1, len(degrees_sorted) + 1) / len(degrees_sorted)
    plt.loglog(degrees_sorted, cumulative, 'ro-', alpha=0.7)
    plt.xlabel('Degree (log scale)')
    plt.ylabel('P(X ≥ k)')
    plt.title('Cumulative Degree Distribution')
    plt.grid(True, alpha=0.3)

    plt.tight_layout()
    plot_path = os.path.join(output_path, f"{file_name}_degree_plots.png")
    plt.savefig(plot_path, dpi=300, bbox_inches='tight')
    plt.close()

    return plot_path

def plot_centrality_histograms(centrality_data, file_name, output_path):
    fig, axes = plt.subplots(1, 3, figsize=(18, 6))
    centrality_types = ['degree', 'betweenness', 'closeness']
    colors = ['lightcoral', 'lightblue', 'lightgreen']

    for i, cent_type in enumerate(centrality_types):
        values = centrality_data[f'{cent_type}_centrality'].values
        axes[i].hist(values, bins=50, alpha=0.7, color=colors[i], edgecolor='black')
        axes[i].set_xlabel(f'{cent_type.capitalize()} Centrality')
        axes[i].set_ylabel('Frequency')
        axes[i].set_title(f'{cent_type.capitalize()} Centrality Distribution')
        axes[i].grid(True, alpha=0.3)

        mean_val = np.mean(values)
        std_val = np.std(values)
        axes[i].text(0.02, 0.98, f'Mean: {mean_val:.4f}\nStd: {std_val:.4f}',
                     transform=axes[i].transAxes, verticalalignment='top',
                     bbox=dict(boxstyle='round', facecolor='white', alpha=0.8))

    plt.suptitle(f'Centrality Distributions - {file_name}', fontsize=16)
    plt.tight_layout()
    plot_path = os.path.join(output_path, f"{file_name}_centrality_histograms.png")
    plt.savefig(plot_path, dpi=300, bbox_inches='tight')
    plt.close()
    return plot_path

print(f"Found {len(files)} files to process: {files}")

all_stats = []

for file_name in files:
    print(f"\nProcessing: {file_name}")
    file_path = os.path.join(input_dir, file_name)

    try:
        df = pd.read_csv(file_path)
    except pd.errors.ParserError:
        try:
            df = pd.read_csv(file_path, sep="\t")
        except:
            try:
                df = pd.read_csv(file_path, delim_whitespace=True)
            except:
                print(f"Error reading {file_name}: Unable to parse file")
                continue

    print(f"File shape: {df.shape}")
    print(f"Available columns: {df.columns.tolist()}")

    if 'protein1' not in df.columns or 'protein2' not in df.columns:
        print(f"Skipping {file_name}: Missing required columns.")
        continue

    has_weights = 'combined_score' in df.columns
    if has_weights:
        print("Combined score column detected (will be ignored for unweighted analysis)")

    base_name = file_name.replace('_filtered.csv', '')
    file_output_dir = os.path.join(output_dir, base_name)
    os.makedirs(file_output_dir, exist_ok=True)

    G = nx.Graph()
    for _, row in df.iterrows():
        G.add_edge(row['protein1'], row['protein2'])

    print(f"Graph created with {G.number_of_nodes()} nodes and {G.number_of_edges()} edges")

    network_stats = calculate_network_stats(G)
    network_stats['file_name'] = file_name
    all_stats.append(network_stats)

    print("Computing centrality measures...")
    degree_centrality = nx.degree_centrality(G)
    betweenness_centrality = nx.betweenness_centrality(G)
    closeness_centrality = nx.closeness_centrality(G)

    centrality_df = pd.DataFrame({
        'protein': list(G.nodes()),
        'degree_centrality': [degree_centrality[node] for node in G.nodes()],
        'betweenness_centrality': [betweenness_centrality[node] for node in G.nodes()],
        'closeness_centrality': [closeness_centrality[node] for node in G.nodes()]
    })

    centrality_measures = ['degree_centrality', 'betweenness_centrality', 'closeness_centrality']
    for cent_type in centrality_measures:
        labels, quartiles = assign_quartiles(centrality_df[cent_type].values)
        centrality_df[f'{cent_type}_quartile'] = labels

    centrality_output_path = os.path.join(file_output_dir, f"{base_name}_detailed_centrality.csv")
    centrality_df.to_csv(centrality_output_path, index=False)

    quartile_summary = []
    for cent_type in centrality_measures:
        for quartile in ['Q1', 'Q2', 'Q3', 'Q4']:
            subset = centrality_df[centrality_df[f'{cent_type}_quartile'] == quartile]
            quartile_summary.append({
                'centrality_type': cent_type,
                'quartile': quartile,
                'count': len(subset),
                'mean': subset[cent_type].mean(),
                'std': subset[cent_type].std(),
                'min': subset[cent_type].min(),
                'max': subset[cent_type].max()
            })

    quartile_df = pd.DataFrame(quartile_summary)
    quartile_output_path = os.path.join(file_output_dir, f"{base_name}_quartile_summary.csv")
    quartile_df.to_csv(quartile_output_path, index=False)

    print("Generating plots...")
    degree_plot_path = plot_degree_distribution(G, base_name, file_output_dir)
    centrality_plot_path = plot_centrality_histograms(centrality_df, base_name, file_output_dir)

    stats_output_path = os.path.join(file_output_dir, f"{base_name}_network_stats.txt")
    with open(stats_output_path, 'w') as f:
        f.write(f"Network Statistics for {file_name}\n")
        f.write("=" * 50 + "\n")
        for key, value in network_stats.items():
            if key != 'file_name':
                f.write(f"{key.replace('_', ' ').title()}: {value}\n")

    print(f"Results saved in: {file_output_dir}")

if all_stats:
    overall_stats_df = pd.DataFrame(all_stats)
    overall_stats_path = os.path.join(output_dir, "overall_network_comparison.csv")
    overall_stats_df.to_csv(overall_stats_path, index=False)
    print(f"\nOverall comparison saved: {overall_stats_path}")

print("\n" + "="*60)
print("All files processed successfully!")
print(f"Results saved in: {output_dir}")
print("="*60)

