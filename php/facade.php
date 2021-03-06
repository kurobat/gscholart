<?php

	require_once("transaction.php");

	class Facade{
	
		private $transaction;
		
		public function __construct(){
			$this->transaction=new Transaction();
		}
		
		//INSERT
		
		public function insertCategory($values){
			$this->transaction->insert("category",$values);
		}
		
		public function insertCountry($values){
			$this->transaction->insert("country",$values);
		}
		
		public function insertJournal($values){
			$this->transaction->insert("journal",$values);
		}
		
		public function insertJournalCategory($values){
			$this->transaction->insert("journalcategory",$values);
		}
		
		//QUERY

		public function retrieveJournalData($values){
			$sql="SELECT sjr,total_docs,h_index FROM journal WHERE title=:title";
			$array=$this->transaction->query($sql,$values);
			return $array;
		}
		public function retrieveCategoriesByCountry($values){
			$sql="SELECT category.id,category.name FROM journal,journalCategory,category,country WHERE journal.issn=journalCategory.id_journal
				  AND journalCategory.id_category=category.id AND journal.id_country=country.id AND country.id=:id GROUP BY category.id 
				  ORDER BY category.name ASC";
			$array=$this->transaction->query($sql,$values);
			return $array;
		}
		
		public function retrieveJournalByCountryAndCategory($values){
			$sql="SELECT journal.title FROM country,category,journal,journalCategory 
				  WHERE journal.issn=journalCategory.id_journal AND journalCategory.id_category=category.id 
				  AND country.id=journal.id_country AND country.id=:id_country AND category.id=:id_category";
			$array=$this->transaction->query($sql,$values);
			return $array;
		}
		
		public function retrieveJournalISSN(){
			$sql="SELECT issn FROM journal";
			$array=$this->transaction->query($sql);
			return $array;
		}
		
		public function retrieveCountries(){
			$sql="SELECT id,name FROM country ORDER BY name ASC";
			$array=$this->transaction->query($sql);
			return $array;
		}
		
		public function retrieveIdCountry($values){
			$sql="SELECT id FROM country WHERE country.name=:name";
			$array=$this->transaction->query($sql,$values);
			return $array;
		}
		
		public function existsJournalCategory($values){
			$sql="SELECT COUNT(*) FROM journalCategory WHERE id_journal=:id_journal AND id_category=:id_category";
			$quantity=$this->transaction->exists($sql,$values);
			return $quantity;
		}
		
		//EXISTS
		
		public function existsCountry($values){
			$sql="SELECT COUNT(*) FROM country WHERE country.name=:name";
			$quantity=$this->transaction->exists($sql,$values);
			return $quantity;
		}
		
		
		public function existsCategory($values){
			$sql="SELECT COUNT(*) FROM category WHERE category.name=:name";
			$quantity=$this->transaction->exists($sql,$values);
			return $quantity;
		}
		
		public function existsJournal($values){
			$sql="SELECT COUNT(*) FROM journal WHERE journal.issn=:issn";
			$quantity=$this->transaction->exists($sql,$values);
			return $quantity;
		}
		
		//ELIMINAR_1
		
		public function retrieveJournalWithoutCategory(){
			$sql="SELECT journal.issn FROM journal LEFT JOIN journalcategory
			      ON journalcategory.id_journal=journal.issn WHERE journalcategory.id_journal IS NULL";
			$array=$this->transaction->query($sql);
			return $array;
		}
		
		public function retrieveCategoriesWithoutJournal(){
			$sql="SELECT category.id FROM category LEFT JOIN journalcategory
				  ON journalcategory.id_category=category.id WHERE journalcategory.id_category IS NULL";
			$array=$this->transaction->query($sql);
			return $array;
		}
		
		public function retrieveCountriesWithoutJournal(){
			$sql="SELECT country.id FROM country LEFT JOIN journal
				  ON journal.id_country=country.id WHERE journal.id_country IS NULL;";
			$array=$this->transaction->query($sql);
			return $array;	
		}
		
		//ELIMINAR_ELIMINAR
		
		public function deleteJournal($values){
			$sql="DELETE FROM journal WHERE issn=:issn";
			$this->transaction->queryTwo($sql,$values);
		}
		
		public function deleteCategory($values){
			$sql="DELETE FROM category WHERE id=:id";
			$this->transaction->queryTwo($sql,$values);
		}
		
		
		public function deleteCountry($values){
			$sql="DELETE FROM country WHERE id=:id";
			$this->transaction->queryTwo($sql,$values);
		}
		
		public function getMessage(){
			return $this->transaction->getMessage();
		}
		
		public function consolidate(){
			return $this->transaction->consolidate();
		}
		
		public function beginTransaction(){
			return $this->transaction->beginTransaction();
		}
	
	}




?>