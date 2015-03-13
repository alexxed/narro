# Narro views #

  * [the project list](https://l10n.mozilla.org/narro/narro_project_list.php) displays the projects available for translation, the translation progress and a few action links
  * [the project text list](https://l10n.mozilla.org/narro/narro_project_text_list.php?p=2) displays all the texts and translations from a project
  * [the project file list](https://l10n.mozilla.org/narro/narro_project_file_list.php?p=2) displays all the files from a project
  * [the file's text list](https://l10n.mozilla.org/narro/narro_file_text_list.php?p=2&f=44) displays all the texts and translations from a file
  * [the project languages page](https://l10n.mozilla.org/narro/narro_project_language_list.php?p=2) displays the list of languages for a specific project and the project translation progress for each language
  * [the languages page](https://l10n.mozilla.org/narro/narro_language_list.php) displays a list of languages available
  * [the translation page](https://l10n.mozilla.org/narro/narro_context_suggest.php?p=2&tf=2&st=1&s=) is the core of the application and the page that is used for translating texts, one by one

# Narro key roles #

  * the **anonymous user** can see all the texts, translations and progress
  * the **registered user** can add translation suggestions, can vote and comment
  * the **validator** looks over translation suggestions for a text and picks the right one to use by validating it

# Narro permissions #

  * Administrator
  * Can add language
  * Can add project
  * Can comment
  * Can delete any suggestion
  * Can delete project
  * Can edit any suggestion
  * Can edit language
  * Can edit project
  * Can export file
  * Can export project
  * Can import file
  * Can import project
  * Can manage project
  * Can manage users
  * Can suggest
  * Can validate
  * Can vote

Permissions are give by language and project, so there's a really good control of the application.

# Narro key components #

  * texts

Narro collects texts in english each time a project is imported. It keeps them forever even if the projects are deleted. Texts do not depend on projects or files.

  * suggestions

For each text, a user can enter any number of translation suggestions. Suggestions are tied only to the original texts. So it's like a dictionary. You have original texts and multiple translations. Suggestion do not depend on project or files.

  * contexts

A text to translate may appear in various places, so it's important to have a good idea where it is used. For that, Narro creates contexts. A context's uniqueness is assured by putting together the project, the file, the original text and the context from the file that surrounds the text to translate.

So a context connects to the link between one original text and many translation suggestions.

For each context, one translation suggestion can be validated.

# Narro suggestions #

You can allow anyone to translate. People like to translate and Narro makes sure that they can translate easy. People makes mistakes; it's ok, here's what Narro can do to keep translation quality high:

  * spellchecking
  * entity checking - this means that if the user forgot to include in the translate &brandShortName; or %s he will be notified to correct this
  * punctuation checking - the user is warned if he forgets an ending character like a dot or if he adds an extra one at the end
  * validation - every translation is read by at least two people, the author and the validator

# Access keys #

Narro detects many access keys so you don't have to deal with them. Users don't have to wonder what does that & from &File does. They will see only File to translate.

When a suggestion with an access key is validated, Narro sets the access key as the same as the one in the original text if the letter is found in the translation; if not the first letter is set. You can change the access key very easy by choosing one letter from a dropdown list.

# Import and export files #

Because not everyone likes working online, any user can work offline by exporting a file, translating it offline and importing it back. It doesn't matter if it's not translated 100% or if it was changed. Narro will try to use everything it finds in the imported file. This does not create conflicts of any kind. Importing a file is equivalent to adding a suggestion for each original text.

Now what if you have an older version of a file that you would like to populate it with translations that exist in Narro ? Yes, you can! Just upload the older version of the file and export the file. You'll get your older version of the file back, but with translations! So you can steal translations anytime!

# Narro files #

Narro works with templates. Templates are actually files with the original texts. Narro takes each text from those file and searches for a valid translation in Narro for that text in that context. If it finds it, it replaces the original text with the translation. That's it, Narro doesn't alter in anyway the original file called a template. This gives you a translated file that has the same formatting and comments as the original file.