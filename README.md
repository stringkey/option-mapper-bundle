# option-mapper-bundle
Symfony Bundle that allows sets of options to be mapped for one domain in different contexts

What? ..

## What problem does this bundle solve

When you have multiple datasources from multiple vendors containing metadata that describes the same object, there can be variations in in the spelling when these objects are described. The identifiers are also unlikely to be the same.

This bundle adds generic functionality to create lists of options that can for example be used in a dropdowns, so far nothing special.

### key features
1. It allows for enumerations (selectable values with a name) that are stored in the database
2. It lets you make the options valid only within a specific context, 
3. It allows you to link the options together between contexts if they semantically mean the same, even though the have slightly differerent descriptive names and different keys

The basic anatomy of an option is display key (the name property) and a unique value (the reference property) similar to an enumeration.
The set of options live in a specific context _MetadataContext_. This names and values are unique within that context.

The options are contained in an OptionGroup that by itself has a name.
Options of different contexts can be linked to each other if they semantically have the same meaning.
An example is a movie genre that can in one context be 'Action and Adventure' and be separate options 
in in another context namely 'Action' and 'Adventure'. 

The linking can be set up in such a way that the
based on an option in one context one or more options can be resolved from another option in the same group.

# Testing
To test the bundle
run
```bash
composer install
```
Then run
```bash
php ./vendor/bin/phpunit
```
or
```bash
php ./vendor/bin/simple-phpunit
```


