SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `forums`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `forums` ;

CREATE  TABLE IF NOT EXISTS `forums` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `description` VARCHAR(255) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `messages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `messages` ;

CREATE  TABLE IF NOT EXISTS `messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `root_id` INT NOT NULL ,
  `dna` VARCHAR(255) NOT NULL ,
  `forum_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_message_ids_forums`
    FOREIGN KEY (`forum_id` )
    REFERENCES `mydb`.`forums` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_message_ids_message_ids`
    FOREIGN KEY (`root_id` )
    REFERENCES `mydb`.`messages` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

CREATE INDEX `fk_message_ids_forums` ON `messages` (`forum_id` ASC) ;

CREATE INDEX `fk_message_ids_message_ids` ON `messages` (`root_id` ASC) ;


-- -----------------------------------------------------
-- Table `message_contents`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `message_contents` ;

CREATE  TABLE IF NOT EXISTS `message_contents` (
  `message_id` INT NOT NULL ,
  `title` VARCHAR(255) NULL ,
  `message` TEXT NULL ,
  `non-html` TEXT NULL ,
  PRIMARY KEY (`message_id`) ,
  CONSTRAINT `fk_message_contents_message_ids`
    FOREIGN KEY (`message_id` )
    REFERENCES `mydb`.`messages` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_message_contents_message_ids` ON `message_contents` (`message_id` ASC) ;


-- -----------------------------------------------------
-- Table `permisions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `permisions` ;

CREATE  TABLE IF NOT EXISTS `permisions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `forums_permisions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `forums_permisions` ;

CREATE  TABLE IF NOT EXISTS `forums_permisions` (
  `forum_id` INT NOT NULL ,
  `permision_id` INT NOT NULL ,
  `add` BOOLEAN NOT NULL DEFAULT false ,
  `delete` BOOLEAN NOT NULL DEFAULT false ,
  `view` BOOLEAN NOT NULL DEFAULT true ,
  `edit` BOOLEAN NOT NULL DEFAULT true ,
  CONSTRAINT `fk_forums_permisions_forums`
    FOREIGN KEY (`forum_id` )
    REFERENCES `mydb`.`forums` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_forums_permisions_permisions`
    FOREIGN KEY (`permision_id` )
    REFERENCES `mydb`.`permisions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_forums_permisions_forums` ON `forums_permisions` (`forum_id` ASC) ;

CREATE INDEX `fk_forums_permisions_permisions` ON `forums_permisions` (`permision_id` ASC) ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
