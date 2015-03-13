The page holds 3 sections:

  * Texts
> Here you can choose the source of texts in the original language
  * Translations
> Optional, here you can specify where are the current files with translated texts
  * Options
> Here you have several options that should help you with the import process

The texts are stored in files, there can be folders, but the structure and file format should be the same for original texts and translated texts. Only the original texts are used to add texts to the project. The translated texts are used only to find matches for the found original texts.

If you keep several versions of a project, you can import the translations from a previous version by selecting another project as source of translations.

Usually, when you press import, a process is launched in background on the server. This means you can leave the page and the import will still be running. If this fails, there will be an attempt to execute the php code directly, but this may result in timeout for large projects.