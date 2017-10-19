require('./bootstrap.js');

new Vue({
    el: '#app',

    data() {
        return {
            searchPhrase: '',
            baseLanguage: langman.baseLanguage,
            selectedLanguage: langman.baseLanguage,
            selectedFile: Object.keys(langman.translations[langman.baseLanguage])[0],
            languages: langman.languages,
            files: Object.keys(langman.translations[langman.baseLanguage]),
            translations: langman.translations,
            selectedKey: null,
            hasChanges: false
        };
    },

    /**
     * The component has been created by Vue.
     */
    mounted() {
        this.addValuesToBaseLanguage();
    },

    computed: {
        /**
         * List of filtered translation keys.
         */
        filteredTranslations() {
            if (this.searchPhrase) {
                return _.chain(this.currentLanguageTranslations)
                    .pickBy(line => {
                        return line.key.toLowerCase().indexOf(this.searchPhrase.toLowerCase()) > -1 || line.value.toLowerCase().indexOf(this.searchPhrase.toLowerCase()) > -1;
                    })
                    .sortBy('value')
                    .value();
            }

            return _.sortBy(this.currentLanguageTranslations, 'value');
        },


        /**
         * List of translation lines from the current language.
         */
        currentLanguageTranslations() {
            return _.map(this.translations[this.selectedLanguage][this.selectedFile], (value, key) => {
                return {key: key, value: value ? value : ''};
            });
        },


        /**
         * List of untranslated keys from the current language.
         */
        currentLanguageUntranslatedKeys() {
            return _.filter(this.translations[this.selectedLanguage][this.selectedFile], value => {
                return !value;
            });
        }
    },


    methods: {
        /**
         * Add a new translation key.
         */
        promptToAddNewKey() {
            var key = prompt("Please enter the new key");

            if (key != null) {
                this.addNewKey(key);
            }
        },


        /**
         * Add a new translation key
         */
        addNewKey(key) {
            if (this.translations[this.baseLanguage][this.selectedFile][key] !== undefined) {
                return alert('This key already exists.');
            }

            _.forEach(this.languages, lang => {
                if (!this.translations[lang][this.selectedFile]) {
                    this.translations[lang][this.selectedFile] = {};
                }

                this.$set(this.translations[lang][this.selectedFile], key, '');
            });
        },


        /**
         * Remove the given key from all languages.
         */
        removeKey(key) {
            if (confirm('Are you sure you want to remove "' + key + '"')) {
                _.forEach(this.languages, lang => {
                    _.forEach(this.files, file => {
                        this.translations[lang][file] = _.omit(this.translations[lang][file], [key]);
                    });
                });

                this.selectedKey = null;
            }
        },


        /**
         * Add a new language file.
         */
        addLanguage() {
            var key = prompt("Enter language key (e.g \"en\")");

            this.languages.push(key);

            if (key != null) {
                $.ajax('/langman/add-language', {
                    data: JSON.stringify({language: key}),
                    headers: {"X-CSRF-TOKEN": langman.csrf},
                    type: 'POST', contentType: 'application/json'
                }).done(_ => {
                    this.languages.push(key);
                })
            }
        },


        /**
         * Save the translation lines.
         */
        save() {
            var self = this;
            $.ajax('/langman/save', {
                data: JSON.stringify({translations: this.translations}),
                headers: {"X-CSRF-TOKEN": langman.csrf},
                type: 'POST', contentType: 'application/json'
            }).done(function () {
                self.hasChanges = false;
                alert('Saved Successfully.');
            })
        },


        /**
         * Collect untranslated strings from project files.
         */
        scanForKeys() {
            $.post('/langman/scan', {_token: langman.csrf})
                .done(response => {
                    if (typeof response === 'object') {

                        console.log(response);
                        Object.assign(this.translations, response);

                        return alert('Langman searched your files & found new keys to translate.');
                    }

                    alert('No new keys were found.');
                })
        },

        /**
         * Add values to the base language used.
         */
        addValuesToBaseLanguage() {
            _.forEach(this.files, file => {
                _.forEach(this.translations[this.baseLanguage][file], (value, key) => {
                    if (!value) {
                        this.translations[this.baseLanguage][file][key] = key;
                    }
                });
            });            
        }
    },

    watch: {
        translations:  {
            handler: function(translations) {
                this.hasChanges = true;
                this.files = Object.keys(translations[this.selectedLanguage]);
            },
            deep: true
        },

        hasChanges: function() {
            if(this.hasChanges) {
                window.onbeforeunload = function () {
                    return 'Are you sure you want to leave?';
                };
            }
            else {
                window.onbeforeunload = null;
            }
        },

        selectedLanguage: function(language) {
            this.files = Object.keys(this.translations[language]);
            this.selectedFile = this.files[0];
        }
    }
});
