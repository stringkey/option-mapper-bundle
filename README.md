# option-mapper-bundle
Symfony Bundle that allows sets of options to be mapped for one domain in different contexts


This bundle adds functionality to create lists of options that can for example be used in a dropdowns, so far nothing special.

The basic anatomy of an option is display key (the name property) and a unique value (the reference property) similar to an enumeration.
The set of options live in a specific context _MetadataContext_. This names and values are unique within that context.

The options are contained in an OptionGroup that by itself has a name.
Options of different contexts can be linked to each other if they semantically have the same meaning.
An example is a movie genre that can in one context be 'Action and Adventure' and be separate options 
in in another context namely 'Action' and 'Adventure'. 

The linking can be set up in such a way that the
based on an option in one context one or more options can be resolved from another option in the same group.