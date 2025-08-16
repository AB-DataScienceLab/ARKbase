library(tidyverse)

clsi_data <- read_xlsx('clsi_vs_fda-breakpoints_2024.xlsx',sheet = 2)


# Filtering CLSI known drugs for BPPL -------------------------------------

# Step1: Defining the list of organisms for each pathogen category

pathogen_organism_groups <- list(
  Acinetobacter_baumannii = c(
    "Acinetobacter species",
    "Acinetobacter species: Acinetobacter baumannii complex only"
  ),
  Escherichia_coli = c(
    "Enterobacterales",
    "Enterobacterales except Salmonella species",
    "Enterobacterales: Citrobacter freundii, Enterobacter cloacae, Escherichia coli, Klebsiella aerogenes, Klebsiella oxytoca, Klebsiella pneumoniae only",
    "Enterobacterales: Citrobacter freundii, Enterobacter cloacae, Escherichia coli, Klebsiella oxytoca, Klebsiella pneumoniae only", # Note: This is duplicated in the source list
    "Enterobacterales: Escherichia coli only",
    "Enterobacterales: Escherichia coli, Klebsiella pneumoniae, Enterobacter cloacae only",
    "Enterobacterales: Escherichia coli, Klebsiella pneumoniae, Proteus mirabilis only"
  ),
  Klebsiella_pneumoniae = c(
    "Enterobacterales",
    "Enterobacterales except Salmonella species",
    "Enterobacterales: Citrobacter freundii, Enterobacter cloacae, Escherichia coli, Klebsiella aerogenes, Klebsiella oxytoca, Klebsiella pneumoniae only",
    "Enterobacterales: Citrobacter freundii, Enterobacter cloacae, Escherichia coli, Klebsiella oxytoca, Klebsiella pneumoniae only",
    "Enterobacterales: Escherichia coli, Klebsiella pneumoniae, Enterobacter cloacae only",
    "Enterobacterales: Escherichia coli, Klebsiella pneumoniae, Proteus mirabilis only",
    "Enterobacterales: Klebsiella pneumoniae and Enterobacter cloacae only",
    "Enterobacterales: Klebsiella pneumoniae only"
  ),
  Shigella_flexneri = c( 
    "Enterobacterales",
    "Enterobacterales: Shigella species only"
  ),
  Shigella_sonnei = c( 
    "Enterobacterales",
    "Enterobacterales: Shigella species only"
  ),
  Enterococcus_faecium = c(
    "Enterococcus faecalis and Enterococcus faecium",
    "Enterococcus species",
    "Enterococcus species: E. faecium only",
    "Enterococcus species: Vancomycin resistant Enterococcus faecium only"
  ),
  Pseudomonas_aeruginosa = c(
    "Pseudomonas aeruginosa"
  ),
  Salmonella_enterica = c(
    "Enterobacterales",
    "Enterobacterales: Salmonella species only"
  ),
  Neisseria_gonorrhoeae = c(
    "Neisseria gonorrhoeae"
  ),
  Staphylococcus_aureus = c(
    "Staphylcoccus aureus",
    "Staphyloccous aureus (MSSA only)",
    "Staphylococcus aureus",
    "Staphylococcus species",
    "Staphylococcus species: Staphylococcus aureus and Staphylococcus lugdunensis",
    "Staphylococcus species: Staphylococcus aureus complex and Staphylococcus lugdunensis",
    "Staphylococcus species: Staphylococcus aureus only",
    "Staphylococcus spp."
  ),
  Streptococcus_pyogenes = c(
    "Streptococcus pyogenes",
    "Streptococcus species other than S. pneumoniae",
    "Beta-hemolytic Streptococcus",
    "Beta-hemolytic Streptococcus: Streptococcus agalactiae, Streptococcus dysgalactiae, Streptococcus pyogenes",
    "Beta-hemolytic Streptococcus: Streptococcus pyogenes",
    "Beta-hemolytic Streptococcus: Streptococcus pyogenes and S. agalactiae"
  ),
  Streptococcus_pneumoniae = c(
    "Streptococcus pneumoniae"
  ),
  Haemophilus_influenzae = c(
    "Haemophilus influenzae",
    "Haemophilus influenzae and H. parainfluenzae",
    "Haemophilus influenzae and Haemophilus parainfluenzae"
  ),
  Streptococcus_agalactiae = c(
    "Beta-hemolytic Streptococcus",
    "Beta-hemolytic Streptococcus: Streptococcus agalactiae, Streptococcus dysgalactiae, Streptococcus pyogenes",
    "Beta-hemolytic Streptococcus: Streptococcus pyogenes and S. agalactiae",
    "Streptococcus agalactiae",
    "Streptococcus species other than S. pneumoniae"
  )
)

# Step 2. Loop through the list and filtering main dataframe.

segregated_data <- lapply(pathogen_organism_groups, function(organism_list) {
  clsi_data %>%
    filter(`Organism/Organism Group` %in% organism_list)
  
})

# Step 3: making one dataframe for all and removing duplicates for each pathogen

combined_clsi_data <- bind_rows(segregated_data, .id = "pathogen_name")

Final_CLSI_data <- combined_clsi_data %>%
  distinct(pathogen_name, `DRUG NAME`, .keep_all = TRUE)

# checking removed rows
removed_rows <- anti_join(combined_clsi_data, Final_CLSI_data)



write_xlsx(Final_CLSI_data,'clsi_data_BPPL.xlsx')


# Adding SMILES structure for each drug -----------------------------------

library(httr)
library(dplyr)
library(progress)

# Step 1: Data preparation

bppl_clsi_df <- readxl::read_xlsx('clsi_data_BPPL.xlsx')

compound_names <- unique(bppl_clsi_df[[2]])

# Creating an empty dataframe to store the results
results_df <- tibble(
  `DRUG NAME` = character(),
  SMILES = character()
)

# Step 2: Loop through names and query the PubChem API directly
# Initialize a progress bar
pb <- progress_bar$new(
  format = "[:bar] :percent | Getting SMILES for :drug",
  total = length(compound_names),
  width = 80
)


for (name in compound_names) {
  #progress bar
  pb$tick(tokens = list(drug = name))
  
  encoded_name <- URLencode(name, reserved = TRUE)
  
  url <- paste0("https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/name/", 
                encoded_name, 
                "/property/SMILES/TXT")
  
  # Make the GET request
  response <- GET(url)
  
  if (status_code(response) == 200) {
    smiles <- content(response, "text", encoding = "UTF-8") %>% trimws()
  } else {
    # If not successful, assign NA
    smiles <- NA_character_
  }
  
  results_df <- results_df %>%
    add_row(`DRUG NAME` = name, SMILES = smiles)
  
  Sys.sleep(0.25) 
}

# Check for any drugs where SMILES were not found
results_df %>% 
  filter(is.na(SMILES)) %>% print(n = 28)

# checked NA values manually and added SMILES and comments 
results_df <- readxl::read_excel('BPPL_known_drug_structure.xlsx')


# Step 3: Join the SMILES data back to the original dataframe
final_df <- left_join(bppl_clsi_df, results_df, by = "DRUG NAME")


writexl::write_xlsx(final_df,"final.xlsx")

