library(tidyverse)

## Mergning the total HPI data with relevant columns  

# Loading data
MINT_again <- read.csv("MINT_again.csv")
HVIDB_again <- read.csv("hvidb_again.csv")
HPIDB_again <- read.csv("hpidb_again.csv")
Virhost_again <- read.csv("virhost_again.csv")
morCVD_again <- read.csv("morCVD_again.csv")
phisto_again <- read.csv("phisto_again.csv")

# Making list of databases to check their column names
database_list <- list(
  HPIDB_again = HPIDB_again,
  HVIDB_again = HVIDB_again,
  MINT_again = MINT_again,
  morCVD_again = morCVD_again,
  phisto_again = phisto_again,
  Virhost_again = Virhost_again
)

lapply(database_list, colnames)


# filtering and meerging full dataset -------------------------------------

# --- 1. Process HPIDB_again ---
hpidb_std <- HPIDB_again %>%
  mutate(Source_Database = "HPIDB") %>% # Add the source database name
  select(
    Host_Protein = host_protein,
    Pathogen_Protein = pathogen_protein,
    Experimental_Method = detection_method, 
    PubMed_ID = pmid,
    Status = Status,
    Host_TaxID = protein_taxid_1,
    Pathogen_TaxID = protein_taxid_2,
    Host_Organism = protein_taxid_1_name,
    Pathogen_Organism = protein_taxid_2_name,
    Interaction_Type = interaction_type,
    Pathogen_type = protein_taxid_2_cat,
    Confidence_Score = confidence,
    Source_Database # Keeping the source column
  ) %>%
  # Ensuring consistent data types 
  mutate(PubMed_ID = as.character(PubMed_ID),
         Confidence_Score = as.character(Confidence_Score)) 

# --- 2. Process HVIDB_again ---
hvidb_std <- HVIDB_again %>%
  mutate(Source_Database = "HVIDB") %>%
  select(
    Host_Protein = host_protein,
    Pathogen_Protein = pathogen_protein,
    Experimental_Method = Experimental_System,
    PubMed_ID = Pubmed_ID,
    Status = Status,
    Host_TaxID = Organism_Interactor_human,
    Pathogen_TaxID = Organism_Interactor_virus,
    Host_Organism = Organism_human, 
    Pathogen_Organism = Organism_virus,
    Interaction_Type = Interaction_Type,
    Source_Database
    # Confidence_Score is missing in the list
  ) %>%
  mutate(PubMed_ID = as.character(PubMed_ID),
         # Adding columns present in others but missing here, fill with NA
         Pathogen_type = "VIRUS",
         Confidence_Score = NA_character_)

# --- 3. Process MINT_again ---
mint_std <- MINT_again %>%
  mutate(Source_Database = "MINT") %>%
  select(
    Host_Protein = host_protein,
    Pathogen_Protein = pathogen_protein,
    Experimental_Method = detection_method,
    PubMed_ID = pmid,
    Status = Status,
    Pathogen_TaxID = taxon_idP,
    Pathogen_Organism = protein_taxid_2,
    Interaction_Type = interaction_type,
    Confidence_Score = confidence,
    Pathogen_type = PathogenCategory,
    Source_Database
  ) %>%
  mutate(PubMed_ID = as.character(PubMed_ID),
         Confidence_Score = as.character(Confidence_Score),
         # Add columns present in others but missing here
         Host_Organism = "human",
         Host_TaxID = "9606")


# --- 4. Process morCVD_again ---
morcvd_std <- morCVD_again %>%
  mutate(Source_Database = "morCVD") %>%
  select(
    Host_Protein = host_protein,
    Pathogen_Protein = pathogen_protein,
    Experimental_Method = INTERACTION.Method,
    PubMed_ID = PUBMED.ID,
    Status = Status,
    Pathogen_TaxID = TAXONOMY,
    Pathogen_Organism = PATHOGEN.NAME,
    Confidence_Score = CONFIDENCE.SCORE,
    Pathogen_type = Category,
    Source_Database
    # Host_TaxID, Host_Organism, Interaction_Type are missing in the list
  ) %>%
  mutate(PubMed_ID = as.character(PubMed_ID),
         Confidence_Score = as.character(Confidence_Score),
         # Add columns present in others but missing here
         Host_TaxID = '9606',
         Host_Organism = 'Human',
         Interaction_Type = NA_character_)

# --- 5. Process phisto_again ---
phisto_std <- phisto_again %>%
  mutate(Source_Database = "phisto") %>%
  select(
    Host_Protein = host_protein,
    Pathogen_Protein = pathogen_protein,
    Experimental_Method = Experimental.Method,
    PubMed_ID = Pubmed.ID,
    Status = Status,
    Pathogen_TaxID = Taxonomy.ID,
    Pathogen_Organism = Pathogen,
    Pathogen_type = Category,
    Source_Database
    # Host_TaxID, Host_Organism, Interaction_Type, Confidence_Score are missing in the list
  ) %>%
  mutate(PubMed_ID = as.character(PubMed_ID),
         Host_TaxID = '9606', 
         Host_Organism = 'Human',
         Interaction_Type = NA_character_,
         Confidence_Score = NA_character_,
         Pathogen_type = NA_character_)


# --- 6. Process Virhost_again ---

Virhost_again$Score_cleaned <- sapply(strsplit(Virhost_again$Score, ",\\s*"), function(x) {
  paste(unique(x), collapse = ", ")
})

virhost_std <- Virhost_again %>%
  mutate(Source_Database = "Virhost") %>%
  select(
    Host_Protein = host_protein,
    Pathogen_Protein = pathogen_protein,
    Experimental_Method = Methods,
    PubMed_ID = Pubmed_id,
    Status = Status,
    Host_TaxID = taxa.id.host,
    Pathogen_TaxID = taxa.id.pathogen,
    Interaction_Type = Interaction.type,
    Confidence_Score = Score_cleaned,
    Source_Database
    # Host_Organism, Pathogen_Organism, Pathogen_type are missing in the list
  ) %>%
  mutate(PubMed_ID = as.character(PubMed_ID),
         Confidence_Score = as.character(Confidence_Score),
         Host_Organism = NA_character_,
         Pathogen_Organism = NA_character_,
         Pathogen_type = 'VIRUS')


# --- 7. Combining all standardized data frames ---

# changing columns to characters from integers where needed
hvidb_std$Host_TaxID <- as.character(hvidb_std$Host_TaxID)
mint_std$Pathogen_TaxID <- as.character(mint_std$Pathogen_TaxID)
morcvd_std$Pathogen_TaxID <- as.character(morcvd_std$Pathogen_TaxID)
phisto_std$Pathogen_TaxID <- as.character(phisto_std$Pathogen_TaxID)



# merging data
merged_hpi_data <- bind_rows(
  hpidb_std,
  hvidb_std,
  mint_std,
  morcvd_std,
  phisto_std,
  virhost_std
)



# --- 8. Group and Summarise ---

# Define the helper function to create "Source: Value" pairs safely
create_source_value_pairs <- function(source_vec, value_vec) {
  
  # Identify rows where the value is actually present (not NA or empty string)
  valid_indices <- !is.na(value_vec) & value_vec != "" # Adjust if empty strings are valid
  
  if (!any(valid_indices)) {
    return(character(0)) # Return empty character vector if no valid values in the group
  }
  
  # Create pairs ONLY for the valid indices
  paste0(source_vec[valid_indices], ": ", value_vec[valid_indices])
}


# --- Group and Summarise ---
aggregated_hpi_data_with_source <- merged_hpi_data %>%
  group_by(Host_Protein, Pathogen_Protein) %>%
  summarise(
    # Experimental Method
    Experimental_Methods_Agg = paste(
      unique(create_source_value_pairs(Source_Database, Experimental_Method)),
      collapse = " | "
    ),
    # PubMed ID
    PubMed_IDs_Agg = paste(
      unique(create_source_value_pairs(Source_Database, PubMed_ID)),
      collapse = " | "
    ),
    # Status
    Status = paste(unique(Status), collapse = ' | '),
    #Host_TaxID
    Host_TaxID_Agg = paste(
      unique(create_source_value_pairs(Source_Database,Host_TaxID)),
      collapse = " | "
    ),
    # Pathogen_TaxID
    Pathogen_TaxID_Agg = paste(
      unique(create_source_value_pairs(Source_Database, Pathogen_TaxID)),
      collapse = " | "
    ),
    # Host Organism
    Host_Organism_Agg = paste(
      unique(create_source_value_pairs(Source_Database, Host_Organism)),
      collapse = " | "
    ),
    # PubMed ID
    Pathogen_Organism_Agg = paste(
      unique(create_source_value_pairs(Source_Database, Pathogen_Organism)),
      collapse = " | "
    ),
    
    # Interaction Type
    Interaction_Types_Agg = paste(
      unique(create_source_value_pairs(Source_Database, Interaction_Type)),
      collapse = " | "
    ),
    
    # Confidence Score
    Confidence_Scores_Agg = paste(
      unique(create_source_value_pairs(Source_Database, Confidence_Score)),
      collapse = " | "
    ),
    
    # --- Keep track of all sources for this pair ---
    Source_Databases = paste(sort(unique(Source_Database)), collapse = ", "),
    Source_Count = n_distinct(Source_Database),
    .groups = 'drop'
  )

write.csv(aggregated_hpi_data_with_source,'merged_data.csv',row.names = FALSE)
