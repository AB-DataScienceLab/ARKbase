<?php
// Include database connection and header
include 'conn3.php';
include 'header.php';

// --- PHP LOGIC UPDATED ---
$table = isset($_GET['source']) && $_GET['source'] === 'ml_data' ? 'ml_data' : 'ml_data_github';

// NEW: Define button text and categories based on the selected source
if ($table === 'ml_data_github') {
    $button_text = 'View Github Page';
    $aware_categories = ['Access', 'Watch', 'Reserve', 'Access/Watch', 'Watch/Reserve', 'Unclassified/Not Recommended'];
} else {
    $button_text = 'View Article';
    $aware_categories = ['Access', 'Watch', 'Watch/Reserve', 'Unclassified/Not Recommended'];
}

$active_category = isset($_GET['category']) && in_array($_GET['category'], $aware_categories) ? $_GET['category'] : $aware_categories[0];
?>

<!-- This main-content class is styled by header.php and positions your content correctly -->
<main class="main-content">
<style>
  /* --- AESTHETIC & INTERACTIVITY OVERHAUL --- */

  /* Defining a beautiful, modern color palette */
  :root {
    --primary-color: #3B71CA; /* A more vibrant blue */
    --primary-color-dark: #1F4E8A;
    --background-color: #f8f9fa; /* Softer page background */
    --surface-color: #ffffff;
    --text-color: #212529;
    --text-muted-color: #6c757d;
    --border-color: #dee2e6;
    --border-radius: 12px; /* Softer, larger radius */
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    --shadow-hover: 0 6px 16px rgba(0, 0, 0, 0.12);

    /* Category-specific colors for a cohesive theme */
    --access-bg: #C9E1CF;  --access-color: #0f5132;
    --watch-bg: #fcf0cb;   --watch-color: #664d03;
    --reserve-bg: #f8d7da; --reserve-color: #842029;
    --watch-reserve-bg: #afc3f0; --watch-reserve-color: #2c5a99;
    --access-watch-bg: #edbe80; --access-watch-color: #7d330f;
    --unclassified-bg: #e2e3e5; --unclassified-color: #41464b;
  }

  /* Main page wrapper for font and color consistency */
  .ml-page-wrapper {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: var(--text-color);
    background-color: var(--background-color);
    line-height: 1.6;
  }

  .container-ml {
    max-width: 1400px; /* Wider container for better data display */
    margin: 2rem auto;
    padding: 2rem;
  }

  .container-ml h1 {
    margin-bottom: 2rem;
    font-size: 2.8em;
    font-weight: 800;
    color: var(--primary-color-dark);
    text-align: center;
  }

  /* --- Source Toggles (GitHub / No-GitHub) --- */
  .source-toggle-menu {
    display: flex;
    justify-content: center;
    margin-bottom: 3rem;
    background-color: #e9ecef;
    border-radius: var(--border-radius);
    padding: 0.5rem;
  }
  .source-toggle-menu form { margin: 0; }
  .source-toggle-button {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border: none;
    border-radius: 8px; /* Slightly smaller radius for inner element */
    background-color: transparent;
    color: var(--text-muted-color);
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    font-size: 1em;
  }
  .source-toggle-button:hover {
    background-color: rgba(0,0,0,0.05);
  }
  .source-toggle-button.active {
    background-color: var(--surface-color);
    color: var(--primary-color);
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
  }
  
  /* --- AWaRe Category Tab Navigation --- */
  .category-tab-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 2.5rem;
  }
  .category-tab-button {
    flex: 1 1 auto;
    padding: 0.8rem 1rem;
    font-weight: 700;
    text-align: center;
    border: 2px solid transparent;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.25s ease;
    font-size: 1.05em;
  }
  .category-tab-button:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
  }
  .category-tab-button.active {
    transform: translateY(-2px) scale(1.02);
    box-shadow: var(--shadow);
    filter: brightness(105%);
  }

  /* Assigning the new color variables to tabs */
  .category-tab-button[data-target='Access'] { background-color: var(--access-bg); color: var(--access-color); border-color: var(--access-color); }
  .category-tab-button[data-target='Watch'] { background-color: var(--watch-bg); color: var(--watch-color); border-color: var(--watch-color); }
  .category-tab-button[data-target='Reserve'] { background-color: var(--reserve-bg); color: var(--reserve-color); border-color: var(--reserve-color); }
  .category-tab-button[data-target='Watch-Reserve'] { background-color: var(--watch-reserve-bg); color: var(--watch-reserve-color); border-color: var(--watch-reserve-color); }
  .category-tab-button[data-target='Access-Watch'] { background-color: var(--access-watch-bg); color: var(--access-watch-color); border-color: var(--access-watch-color); }
  .category-tab-button[data-target='Unclassified-Not-Recommended'] { background-color: var(--unclassified-bg); color: var(--unclassified-color); border-color: var(--unclassified-color); }
  
  /* --- Content Panes --- */
  .category-content-pane {
    display: none;
    padding: 2.5rem;
    border-radius: var(--border-radius);
    animation: fadeIn 0.5s ease-in-out;
    transition: background-color 0.4s ease;
  }
  .category-content-pane.active { display: block; }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Matching pane background to the active tab's color */
  #pane-Access.active { background-color: var(--access-bg); }
  #pane-Watch.active { background-color: var(--watch-bg); }
  #pane-Reserve.active { background-color: var(--reserve-bg); }
  #pane-Watch-Reserve.active { background-color: var(--watch-reserve-bg); }
  #pane-Access-Watch.active { background-color: var(--access-watch-bg); }
  #pane-Unclassified-Not-Recommended.active { background-color: var(--unclassified-bg); }
  
  /* --- Data Display (Antibiotics & Pathogen Cards) --- */
  .antibiotic-group { margin-bottom: 3rem; }
  .antibiotic-name {
    font-size: 1.75em;
    font-weight: 700;
    margin: 0 0 1.5rem 0;
    padding-bottom: 0.75rem;
    color: var(--primary-color-dark);
    border-bottom: 2px solid var(--primary-color);
  }
  
  .pathogen-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
  }
  
  .pathogen-card {
    background: var(--surface-color);
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
    display: flex;
    flex-direction: column;
  }
  .pathogen-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
  }
  
  .pathogen-card-content { flex-grow: 1; }
  .pathogen-card-content div { margin-bottom: 0.75rem; }
  .pathogen-card-content strong {
    color: var(--text-muted-color);
    font-weight: 500;
    margin-right: 8px;
  }
  .pathogen-card-content span { font-weight: 600; }
  .pathogen-card-content .pathogen-name { font-size: 1.2em; font-style: italic; color: var(--text-color); }

  .pathogen-card-footer { margin-top: 1rem; }
  .pathogen-link-button {
    display: inline-block;
    background-color: var(--primary-color);
    color: #fff;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.2s ease;
  }
  .pathogen-link-button:hover {
    background-color: var(--primary-color-dark);
    color: #fff;
  }
</style>

<div class="ml-page-wrapper">
  <div class="container-ml">
    <h1>Curated Machine Learning Models</h1>

    <div class="source-toggle-menu">
      <form method="get" action="">
        <input type="hidden" name="source" value="ml_data_github">
        <button type="submit" class="source-toggle-button <?= $table === 'ml_data_github' ? 'active' : '' ?>">With GitHub Repositories</button>
      </form>
      <form method="get" action="">
        <input type="hidden" name="source" value="ml_data">
        <button type="submit" class="source-toggle-button <?= $table === 'ml_data' ? 'active' : '' ?>">Without GitHub Repositories</button>
      </form>
    </div>

    <div class="category-tab-nav" id="category-tabs">
      <?php foreach ($aware_categories as $category):
          $data_target = str_replace(['/', ' '], '-', $category);
        ?>
        <button class="category-tab-button <?= $category === $active_category ? 'active' : '' ?>" data-target="<?= htmlspecialchars($data_target) ?>">
          <?= htmlspecialchars($category) ?>
        </button>
      <?php endforeach; ?>
    </div>
    
    <div class="category-content-container">
      <?php foreach ($aware_categories as $category): ?>
        <?php
          if ($category === 'Unclassified/Not Recommended') {
              $where_clause = "AWaRe_Category IN ('Unclassified', 'Not Recommended')";
          } else {
              $category_escaped = mysqli_real_escape_string($conn, $category);
              $where_clause = "AWaRe_Category = '$category_escaped'";
          }
          
          $data_target = str_replace(['/', ' '], '-', $category);

          $antibiotics_query = "
              SELECT Antibiotics, COUNT(*) as count 
              FROM $table 
              WHERE $where_clause
              GROUP BY Antibiotics 
              ORDER BY count DESC";
          $antibiotics_result = mysqli_query($conn, $antibiotics_query);
        ?>
        <div id="pane-<?= htmlspecialchars($data_target) ?>" class="category-content-pane <?= $category === $active_category ? 'active' : '' ?>">
          <?php if ($antibiotics_result && mysqli_num_rows($antibiotics_result) > 0): ?>
            <?php while ($ab_row = mysqli_fetch_assoc($antibiotics_result)): ?>
              <?php $antibiotic = $ab_row['Antibiotics']; ?>
              <div class="antibiotic-group">
                <h2 class="antibiotic-name"><?= htmlspecialchars($antibiotic) ?></h2>
                <div class="pathogen-grid">
                  <?php
                  $antibiotic_escaped = mysqli_real_escape_string($conn, $antibiotic);
                  $subquery = "
                      SELECT * FROM $table 
                      WHERE Antibiotics = '$antibiotic_escaped' 
                      AND $where_clause";
                  $subresult = mysqli_query($conn, $subquery);
                  while ($row = mysqli_fetch_assoc($subresult)) {
                    echo "<div class='pathogen-card'>";
                    echo "<div class='pathogen-card-content'>";
                    echo "<div><strong>Pathogen:</strong> <span class='pathogen-name'>" . htmlspecialchars(trim($row['Pathogens'])) . "</span></div>";
                    echo "<div><strong>Algorithm:</strong> <span>" . htmlspecialchars($row['Algorithms']) . "</span></div>";
                    echo "<div><strong>Score:</strong> <span>" . htmlspecialchars($row['Score']) . "</span></div>";
                    echo "</div>";
                    echo "<div class='pathogen-card-footer'>";
                    // --- BUTTON TEXT IS NOW DYNAMIC ---
                    echo "<a class='pathogen-link-button' href='" . htmlspecialchars($row['Link']) . "' target='_blank'>" . htmlspecialchars($button_text) . "</a>";
                    echo "</div>";
                    echo "</div>";
                  }
                  ?>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No data available for this category.</p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- (JavaScript is unchanged) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tabContainer = document.getElementById('category-tabs');
  const tabs = tabContainer.querySelectorAll('.category-tab-button');
  const contentPanes = document.querySelectorAll('.category-content-pane');

  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      const currentActiveTab = tabContainer.querySelector('.active');
      if (currentActiveTab) {
        currentActiveTab.classList.remove('active');
      }
      
      this.classList.add('active');
      const targetPaneId = 'pane-' + this.dataset.target;

      contentPanes.forEach(pane => {
        pane.classList.remove('active');
      });
      
      const targetPane = document.getElementById(targetPaneId);
      if (targetPane) {
        targetPane.classList.add('active');
      }

      const url = new URL(window.location);
      const categoryForURL = this.textContent.trim();
      url.searchParams.set('category', categoryForURL);
      window.history.pushState({}, '', url);
    });
  });
});
</script>
</main>

<?php include 'footer.php'; ?>