-- install sql for optionvaluevisibility extension, alter table civicrm_price_field_value to add column

ALTER TABLE `civicrm_price_field_value` ADD COLUMN `min_deposit` decimal(20,2) DEFAULT NULL COMMENT 'Minimum deposit for this option';