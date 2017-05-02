require('./bootstrap.js');

new Vue({
    el: '#app',

    data() {
        return {
            searchPhrase: '',
            baseLanguage: langman.baseLanguage,
            selectedLanguage: langman.baseLanguage,
            languages: langman.languages,
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

        this.confirmBeforeLeavingWithChanges('translations');
    },

    computed: {
        /**
         * List of filtered translation keys.
         */
        filteredTranslations() {
            if (this.searchPhrase) {
                return _.chain(this.currentLanguageTranslations)
                    .pickBy(line => {
                        return line.key.toLowerCase().indexOf(this.searchPhrase.toLowerCase()) > -1;
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
            return _.map(this.translations[this.selectedLanguage], (value, key) => {
                return {key: key, value: value ? value : ''};
            });
        },


        /**
         * List of untranslated keys from the current language.
         */
        currentLanguageUntranslatedKeys() {
            return _.filter(this.translations[this.selectedLanguage], value => {
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
            if (this.translations[this.baseLanguage][key] !== undefined) {
                return alert('This key already exists.');
            }

            _.forEach(this.languages, lang => {
                if (!this.translations[lang]) {
                    this.translations[lang] = {};
                }

                this.$set(this.translations[lang], key, '');
            });

            this.addValuesToBaseLanguage();
        },


        /**
         * Remove the given key from all languages.
         */
        removeKey(key) {
            if (confirm('Are you sure you want to remove "' + key + '"')) {
                _.forEach(this.languages, lang => {
                    this.translations[lang] = _.omit(this.translations[lang], [key]);
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
            $.ajax('/langman/save', {
                data: JSON.stringify({translations: this.translations}),
                headers: {"X-CSRF-TOKEN": langman.csrf},
                type: 'POST', contentType: 'application/json'
            }).done(function () {
                alert('Saved Successfully.');
            })
        },


        /**
         * Collect untranslated strings from project files.
         */
        scanForKeys() {
            $.post('/langman/scan', {_token: langman.csrf})
                .done(response => {
                    if (response.length) {
                        _.forEach(response, key => {
                            this.addNewKey(key);
                        });

                        this.addValuesToBaseLanguage();

                        return alert('Langman searched your files & found new keys to translate.');
                    }

                    alert('No new keys were found.');
                })
        },


        /**
         * Ask the user for confirmation before leaving if changes exist.
         */
        confirmBeforeLeavingWithChanges(objectToWatch) {
            this.$watch(objectToWatch, function () {
                this.hasChanges = true;

                if (!window.onbeforeunload) {
                    window.onbeforeunload = function () {
                        return 'Are you sure you want to leave?';
                    };
                }
            }, {deep: true});
        },


        /**
         * Add values to the base language used.
         */
        addValuesToBaseLanguage() {
            _.forEach(this.translations[this.baseLanguage], (value, key) => {
                if (!value) {
                    this.translations[this.baseLanguage][key] = key;
                }
            });
        }
    }
});
