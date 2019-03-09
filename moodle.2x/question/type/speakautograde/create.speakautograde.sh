cp -r m/34/question/type/essayautograde Desktop/essayautograde

rm -fR Desktop/essayautograde/.svn

find Desktop/essayautograde -type f \
    \( -name "*.php" -o -name "*.txt" -o -name "*.css" -o -name "*.js" -o -name "*.xml" \) \
    -exec perl -p -i -e 's/essayautograde/speakautograde/g' {} \;

find Desktop/essayautograde -type f \
    \( -name "*.php" -o -name "*.txt" -o -name "*.css" -o -name "*.js" -o -name "*.xml" \) \
    -exec perl -p -i -e 's/essay/speak/g' {} \;

find Desktop/essayautograde -type f \
    \( -name "*.php" -o -name "*.txt" -o -name "*.css" -o -name "*.js" -o -name "*.xml" \) \
    -exec perl -p -i -e 's/Essay/Speak/g' {} \;

find Desktop/essayautograde -type f \
    \( -name "*.php" -o -name "*.txt" -o -name "*.css" -o -name "*.js" -o -name "*.xml" \) \
    -exec perl -p -i -e 's/ESSAY/SPEAK/g' {} \;

mv Desktop/essayautograde/backup/moodle2/backup_qtype_essayautograde_plugin.class.php Desktop/essayautograde/backup/moodle2/backup_qtype_speakautograde_plugin.class.php
mv Desktop/essayautograde/backup/moodle2/restore_qtype_essayautograde_plugin.class.php Desktop/essayautograde/backup/moodle2/restore_qtype_speakautograde_plugin.class.php
mv Desktop/essayautograde/edit_essayautograde_form.php Desktop/essayautograde/edit_speakautograde_form.php
mv Desktop/essayautograde/lang/en/qtype_essayautograde.php Desktop/essayautograde/lang/en/qtype_speakautograde.php
mv Desktop/essayautograde/lang/es/qtype_essayautograde.php Desktop/essayautograde/lang/es/qtype_speakautograde.php
mv Desktop/essayautograde/lang/es_mx/qtype_essayautograde.php Desktop/essayautograde/lang/es_mx/qtype_speakautograde.php
mv Desktop/essayautograde/amd/src/essayautograde.js Desktop/essayautograde/amd/src/speakautograde.js
mv Desktop/essayautograde/amd/build/essayautograde.min.js Desktop/essayautograde/amd/build/speakautograde.min.js
mv Desktop/essayautograde Desktop/speakautograde
