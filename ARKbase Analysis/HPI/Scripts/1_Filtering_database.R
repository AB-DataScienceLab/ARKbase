library(tidyverse)

# MINT --------------------------------------------------------------------
#loading data
MINT <- readxl::read_excel('Raw data/MINT.xlsx', sheet = 1)

#1 checking and filtering human taxa if they are present in taxa id pathogen column

colnames(MINT)

data_to_flip <- MINT |> 
  filter( protein_taxid_2 == 'taxid:9606(human)|taxid:9606(Homo sapiens)' & protein_taxid_1 != 'taxid:9606(human)|taxid:9606(Homo sapiens)' )

# fixing 14663 rows with flipped data
data_to_flip <- data_to_flip |>
  # Create temporary columns to hold the host-side data
  mutate(
    temp_host_protein = `HOST Protein`,  
    temp_host_alternative_id = `alternative_identifiers_1`,
    temp_host_alias = `protein_alias_1`,  
    temp_host_taxid = `protein_taxid_1`,
    
    # Flip the host and pathogen columns
    `HOST Protein` = `Pathogen Protein`, 
    `alternative_identifiers_1` = `alternative_identifiers_2`,
    `protein_alias_1` = `protein_alias_2`,
    `protein_taxid_1` = `protein_taxid_2`,
    
    # Assign the temp columns to pathogen columns
    `Pathogen Protein` = temp_host_protein,  
    `alternative_identifiers_2` = temp_host_alternative_id,  
    `protein_alias_2` = temp_host_alias,  
    `protein_taxid_2` = temp_host_taxid
  ) |> 
  # Dropping the temporary columns
  select(-temp_host_protein, -temp_host_alternative_id, -temp_host_alias, -temp_host_taxid)


# adding fixed data back to main data
modified_MINT <- MINT |> 
  filter(!(  protein_taxid_2 == 'taxid:9606(human)|taxid:9606(Homo sapiens)' & protein_taxid_1 != 'taxid:9606(human)|taxid:9606(Homo sapiens)' ))

flipped_fix_data <- modified_MINT |> bind_rows(data_to_flip)

#2 adding same HPI interactions in one column with interactions separated by comma.

unique_data <- MINT %>%
  group_by(`HOST Protein`, `Pathogen Protein`) %>%
  summarise(
    `alternative_identifiers_1` = paste((`alternative_identifiers_1`), collapse = ", "),
    `alternative_identifiers_2` = paste((`alternative_identifiers_2`), collapse = ", "),
    `protein_alias_1` = paste((`protein_alias_1`), collapse = ", "),
    `protein_alias_2` = paste((`protein_alias_2`), collapse = ", "),
    `detection_method` = paste((`detection_method`), collapse = ", "),
    `author_name` = paste((`author_name`), collapse = ", "),
    `pmid` = paste((`pmid`), collapse = ", "),
    `protein_taxid_1` = paste(unique(`protein_taxid_1`), collapse = ", "),
    `protein_taxid_2` = paste(unique(`protein_taxid_2`), collapse = ", "),
    `interaction_type` = paste((`interaction_type`), collapse = ", "),
    `source_database_id` = paste((`source_database_id`), collapse = ", "),
    `database_identifier` = paste((`database_identifier`), collapse = ", "),
    `confidence` = paste((`confidence`), collapse = ", "),
    Status = paste(unique(Status), collapse = ", "),
    taxon_idP = paste(unique(taxon_idP), collapse = ", "),
    PathogenCategory = paste(unique(PathogenCategory), collapse = ", ")
  ) %>%
  ungroup()

write.csv(unique_data,'final result/MINT_again.csv',row.names = FALSE)




#3 Unique human host and removing human interactome data
human_data <- unique_data |> 
  filter( protein_taxid_1 =='taxid:9606(human)|taxid:9606(Homo sapiens)') |> 
  filter(!( protein_taxid_1 == 'taxid:9606(human)|taxid:9606(Homo sapiens)' & protein_taxid_2 == 'taxid:9606(human)|taxid:9606(Homo sapiens)'))

#only 17099 interactions left

#4 mapping human host protein to uniprot

human_data$`HOST Protein` <- gsub("intact:", "", human_data$`HOST Protein`)
human_data$`Pathogen Protein` <- gsub("intact:", "", human_data$`Pathogen Protein`)

uniquehostprotein <- unique(human_data$`HOST Protein`)
writeLines(uniquehostprotein,'result/MINThostprotein.txt')

protein_status <- readxl::read_excel('Raw data/HostStatusMINT.xlsx')

interaction_data_with_status <- human_data |> 
  left_join(protein_status, by = "HOST Protein")


duplicates_in_protein_status <- protein_status[duplicated(protein_status$`HOST Protein`), ]

protein_status_clean <- protein_status %>%
  distinct(`HOST Protein`, .keep_all = TRUE)

interaction_data_with_status <- human_data |> 
  left_join(protein_status_clean, by = "HOST Protein")

#saving data
write.csv(interaction_data_with_status,'result/MINT_final.csv',row.names = FALSE)
write_xlsx(unique_data,'result/mergedMINT.xlsx')





# HPIDb -------------------------------------------------------------------

#loading data



#renaming protein_xref_1_unique and protein_xref_2_unique as host and pathogen proteins
colnames(hpidb1)[colnames(hpidb1) == "protein_xref_2_unique"] <- 'Pathogen Proteins'
colnames(hpidb1)[colnames(hpidb1) == "protein_xref_1_unique"] <- 'Host Proteins'

hpidb1 <- hpidb1 |> 
      select(`Host Proteins`, `Pathogen Proteins`, everything())



#1 adding same HPI interactions in one column with interactions separated by comma.

unique_data <- hpidb %>%
  group_by(`Host Proteins`,`Pathogen Proteins`) %>%
  summarise(
    `alternative_identifiers_1` = paste(unique(`alternative_identifiers_1`), collapse = ", "),
    `alternative_identifiers_2` = paste(unique(`alternative_identifiers_2`), collapse = ", "),
    `protein_alias_1` = paste(unique(`protein_alias_1`), collapse = ", "),
    `protein_alias_2` = paste(unique(`protein_alias_2`), collapse = ", "),
    `detection_method` = paste((`detection_method`), collapse = ", "),
    `author_name` = paste((`author_name`), collapse = ", "),
    `pmid` = paste((`pmid`), collapse = ", "),
    `protein_taxid_1` = paste(unique(`protein_taxid_1`), collapse = ", "),
    `protein_taxid_2` = paste(unique(`protein_taxid_2`), collapse = ", "),
    `interaction_type` = paste((`interaction_type`), collapse = ", "),
    `source_database_id` = paste((`source_database_id`), collapse = ", "),
    `database_identifier` = paste((`database_identifier`), collapse = ", "),
    `confidence` = paste((`confidence`), collapse = ", "),
    `# protein_xref_1` = paste(unique(`# protein_xref_1`), collapse = ", "),
    `protein_xref_2` = paste(unique(`protein_xref_2`), collapse = ", "),
    `protein_taxid_1_cat` = paste(unique(`protein_taxid_1_cat`), collapse = ", "),
    `protein_taxid_2_cat` = paste(unique(`protein_taxid_2_cat`), collapse = ", "),
    `protein_taxid_1_name` = paste(unique(`protein_taxid_1_name`), collapse = ", "),
    `protein_taxid_2_name` = paste(unique(`protein_taxid_2_name`), collapse = ", "),
    `source_database` = paste(unique(`source_database`), collapse = ", "),
    `protein_xref_1_display_id` = paste(unique(`protein_xref_1_display_id`), collapse = ", "),
    `protein_xref_2_display_id` = paste(unique(`protein_xref_2_display_id`), collapse = ", "),
    Status = paste(unique(Status), collapse = ", ")
  ) %>%
  ungroup()

hpidb |> anti_join(
  hpidb |> distinct(`Host Proteins`,`Pathogen Proteins`, .keep_all = TRUE)
)
write.csv(unique_data,'final result/hpidb_again.csv',row.names = FALSE)


write_xlsx(unique_data,'uniquehpidbdata.xlsx')

#2. only human as host
human_data <- unique_data |> 
  filter(protein_taxid_1 == 'taxid:9606(human|Homo sapiens)'| protein_taxid_1 == 'taxid:9606(Homo sapiens|Human)' | protein_taxid_1 == 'taxid:9606(Homo sapiens)')



#3. Uniprot mapping

# removing UNIPROT_AC and INTACT from front of IDs
human_data$`Host Proteins` <- gsub("UNIPROT_AC:", "", human_data$`Host Proteins`)
human_data$`Pathogen Proteins` <- gsub("UNIPROT_AC:", "", human_data$`Pathogen Proteins`)

human_data$`Host Proteins` <- gsub("INTACT:", "", human_data$`Host Proteins`)
human_data$`Pathogen Proteins` <- gsub("INTACT:", "", human_data$`Pathogen Proteins`)


uniquehostprotein <- unique(human_data$`Host Proteins`)
writeLines(uniquehostprotein,'result/hostuniquehpi.txt')

protein_status <- readxl::read_excel('Raw data/hpidbhoststatus.xlsx')



duplicates_in_human_data <- human_data[duplicated(human_data$`Host Proteins`), ]
duplicates_in_protein_status <- protein_status[duplicated(protein_status$`Host Proteins`), ]


protein_status_clean <- protein_status %>%
  distinct(`Host Proteins`, .keep_all = TRUE)

interaction_data_with_status <- human_data |> 
  left_join(protein_status_clean, by = "Host Proteins")

write.csv(interaction_data_with_status,'result/HPIDB_final.csv',row.names = FALSE)
write.csv(unique_data,'result/mergedHPIdb.csv')







# PHISTO ------------------------------------------------------------------

#loading data

PHISTROvirus <- read_excel('Raw data/phi_data_virus.xls')
PHISTObacteria <- read_excel('Raw data/phi_data_bacteria.xls')
PHISTOraw2 <-  PHISTROvirus |> bind_rows(PHISTObacteria)

colnames(PHISTOraw2)[colnames(PHISTOraw2) == "Uniprot ID...3"] <- 'Pathogen Protein'
colnames(PHISTOraw2)[colnames(PHISTOraw2) == "Uniprot ID...5"] <- 'Host Proteins'

PHISTOraw2 <- PHISTOraw2 |> select(`Host Proteins`,`Pathogen Protein`,everything())

#1 checking and filtering human taxa if they are present in taxa id pathogen column

PHISTOraw2 |> 
  filter(`Taxonomy ID` == '9606') |> select(`Pathogen Protein`) |> unique()



#2 adding same HPI interactions in one column with interactions separated by comma.

unique_data <- phisto %>%
  group_by(`Host Proteins`, `Pathogen Protein`) %>%
  summarise(
    `Pathogen` = paste(unique(`Pathogen`), collapse = ", "),
    `Taxonomy ID` = paste(unique(`Taxonomy ID`), collapse = ", "),
    `Pathogen Protein_Name` = paste(unique(`Pathogen Protein_Name`), collapse = ", "),
    `Experimental Method` = paste((`Experimental Method`), collapse = ", "),
    `Pubmed ID` = paste((`Pubmed ID`), collapse = ", "),
    `Human Protein` = paste(unique(`Human Protein`), collapse = ", "),
    Status = paste(unique(Status), collapse = ", ")
  ) %>%
  ungroup()

write.csv(unique_data,'final result/phisto_again.csv',row.names = FALSE)


#3 mapping human host protein to uniprot

#getting unique human host protein list
uniquehostprotein <- unique(unique_data$`Host Proteins`)
writeLines(uniquehostprotein,'result/PHISTOhostprotein.txt')

protein_status <- readxl::read_excel('Raw data/HostStatusPHISTO.xlsx')

interaction_data_with_status <- unique_data |> 
  left_join(protein_status, by = "Host Proteins")


duplicates_in_protein_status <- protein_status[duplicated(protein_status$`Host Proteins`), ]

protein_status_clean <- protein_status %>%
  distinct(`Host Proteins`, .keep_all = TRUE)

interaction_data_with_status <- unique_data |> 
  left_join(protein_status_clean, by = 'Host Proteins')

# checking data

unique(interaction_data_with_status$Status)
sum(is.na(interaction_data_with_status$Status))
sum(interaction_data_with_status$Status == "reviewed", na.rm = TRUE)
sum(interaction_data_with_status$Status == "unreviewed", na.rm = TRUE)
sum(interaction_data_with_status$Status == "obsolete", na.rm = TRUE)

# saving the data
write.csv(PHISTROvirus,'result/PHISTO_Raw.csv',row.names = FALSE)



# MorCVD ------------------------------------------------------------------

#loading data
morCVD_raw <- read_excel('Raw data/MORCVD_Raw.xlsx', sheet = 1)
morCVD_raw_interactions <- read_excel('Raw data/MORCVD_FINAL_ALL_METHODS_.xlsx')

#1. Merging the morcvd raw data with interaction data

#1.a First removing duplicates from both morcvd raw and interaction data
morCVD_raw_interactions_2 <- morCVD_raw_interactions |> 
                  group_by(`HOST PROTEIN`, `PATHOGEN PROTEIN`) %>%
                  summarise(
                    `INTERACTION Method` = paste((`INTERACTION Method`), collapse = ", ")
                  ) |> 
                  ungroup()



unique_data <- morCVD %>%
  group_by(`HOST PROTEIN`, `PATHOGEN PROTEIN`) %>%
  summarise(
    `host_links` = paste(unique(`host_links`), collapse = ", "),
    `path_links` = paste(unique(`path_links`), collapse = ", "),
    `PATHOGEN NAME` = paste(unique(`PATHOGEN NAME`), collapse = ", "),
    `TAXONOMY` = paste(unique(`TAXONOMY`), collapse = ", "),
    `PUBMED ID` = paste((`PUBMED ID`), collapse = ", "),
    `GENE SYMBOL HOST` = paste(unique(`GENE SYMBOL HOST`), collapse = ", "),
    `GENE SYMBOL PATHOGEN` = paste(unique(`GENE SYMBOL PATHOGEN`), collapse = ", "),
    `HOST UNIPROT ENTRY` = paste((`HOST UNIPROT ENTRY`), collapse = ", "),
    `PATHOGEN UNIPROT ENTRY` = paste((`PATHOGEN UNIPROT ENTRY`), collapse = ", "),
    `SOURCE DATABASE` = paste(unique(`SOURCE DATABASE`), collapse = ", "),
    `CONFIDENCE SCORE` = paste(unique(`CONFIDENCE SCORE`), collapse = ", "),
    `INTERACTION Method` = paste((`INTERACTION Method`), collapse = ", "),
    Status = paste(unique(Status), collapse = ", "),
    Category = paste(unique(Category),collapse = ", ")
  ) %>%
  ungroup()

write.csv(unique_data,'final result/morCVD_again.csv',row.names = FALSE)


merged_data <- unique_data %>%
  left_join(morCVD_raw_interactions_2, by = c("HOST PROTEIN", "PATHOGEN PROTEIN"))


#the interaction which is present in interaction data but not present in Main data
morCVD_raw_interactions_2 %>%
  anti_join(unique_data, by = c("HOST PROTEIN", "PATHOGEN PROTEIN"))

# O00572         P68467             isothermal titration calorimetry




#2 mapping human host protein to uniprot
uniquehostprotein <- unique(merged_data$`HOST PROTEIN`)
writeLines(uniquehostprotein,'result/MorCVDhostprotein.txt')

protein_status <- readxl::read_excel('morCVDhoststatus.xlsx')

interaction_data_with_status <- merged_data |> 
  left_join(protein_status, by = "HOST PROTEIN")

duplicates_in_protein_status <- protein_status[duplicated(protein_status$`HOST PROTEIN`), ]

protein_status_clean <- protein_status %>%
  distinct(`HOST PROTEIN`, .keep_all = TRUE)

interaction_data_with_status <- merged_data |> 
  left_join(protein_status_clean, by = 'HOST PROTEIN')

# checking data

unique(interaction_data_with_status$Status)
sum(is.na(interaction_data_with_status$Status))
sum(interaction_data_with_status$Status == "reviewed", na.rm = TRUE)
sum(interaction_data_with_status$Status == "unreviewed", na.rm = TRUE)
sum(interaction_data_with_status$Status == "obsolete", na.rm = TRUE)

#saving data
write.csv(unique_data,'interactionmerged.csv', row.names = FALSE)
