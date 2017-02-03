<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Langman GUI</title>

    <!-- Style sheets-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' rel='stylesheet' type='text/css'>

    <!-- JavaScript -->
    <script
            src="https://code.jquery.com/jquery-3.1.1.min.js"
            integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/lodash/4.17.4/lodash.min.js"></script>

    <style>
        body {
            background-color: #f2f7f9;
        }

        .langCheckboxes label {
            font-weight: normal;
            margin-left: 15px;
        }

        .navbar-default {
            background-color: #d7e5ea;
            border-color: #b8d0d8;
            border-radius: 0;
            border-top: none;
        }

        .navbar-default .navbar-brand,
        .navbar-default .navbar-nav > li > a {
            color: #345967;
        }

        .navbar-default .navbar-nav > .open > a,
        .navbar-default .navbar-nav > .open > a:focus,
        .navbar-default .navbar-nav > .open > a:hover {
            color: #224a67;
            background-color: #bed1d8;
        }
    </style>
</head>
<body>
<div id="app" v-cloak>

    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <span class="navbar-brand">Laravel Langman GUI</span>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


                <p class="navbar-text">
                    @{{ _.toArray(currentLanguageTranslations).length }} Keys
                </p>

                <p class="navbar-text" v-if="_.toArray(currentLanguageUntranslatedKeys).length ">
                    <span class="text-danger">
                        @{{ _.toArray(currentLanguageUntranslatedKeys).length }} Un-translated
                    </span>
                </p>

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" role="button"
                           class="dropdown-toggle"
                           aria-haspopup="true"
                           aria-expanded="false">
                            Language: @{{ selectedLanguage }}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li v-for="lang in languages" @click="selectedLanguage = lang"><a href="#">@{{ lang }}</a></li>
                        </ul>
                    </li>
                    <li><a href="#" v-on:click="sync">Scan</a></li>
                    <li><a href="#" v-on:click="addNewKey">New Key</a></li>
                    <li><a href="#" v-on:click="save">Save</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">Search</div>
                    <input type="text" class="form-control" v-model="searchPhrase">
                </div>

                <ul class="list-group" style="margin-top:20px; overflow: scroll; height: 500px;">
                    <a href="#" v-for="line in filteredTranslations"
                       v-on:click="selectedKey = line.key"
                       :class="['list-group-item', {'list-group-item-danger': !line.value}]">
                        <h5 class="list-group-item-heading">@{{ line.key }}</h5>
                        <p class="list-group-item-text">@{{ line.value }}</p>
                    </a>
                </ul>
            </div>
            <div class="col-sm-6">

                <div v-if="selectedKey">
                    <div class="well">
                        @{{ selectedKey }}
                    </div>

                    <textarea rows="10" class="form-control" v-model="translations[selectedLanguage][selectedKey]"></textarea>

                    <hr>

                    <a class="text-danger" href="#" v-on:click="removeKey(selectedKey)">Remove this key</a>
                </div>

                <h4 class="text-muted text-center" v-else>
                    .<br>
                    .<br>
                    .<br><br>
                    Select a key from the list to the left
                </h4>

            </div>
        </div>
    </div>

</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            searchPhrase: '',
            baseLanguage: '{!! config('langmanGUI.base_language') !!}',
            selectedLanguage: '{!! config('langmanGUI.base_language') !!}',
            languages: {!! json_encode($languages) !!},
            translations: {!! json_encode($translations) !!},
            selectedKey: null
        },


        /**
         * The Vue component is ready.
         */
        mounted: function () {
            var that = this;

            _.forEach(this.translations[this.baseLanguage], function (value, key) {
                if (!value) {
                    that.translations[that.baseLanguage][key] = key;
                }
            });
        },


        computed: {
            /**
             * List of filtered translation keys.
             */
            filteredTranslations: function () {
                var that = this;

                if (this.searchPhrase) {
                    return _.chain(this.currentLanguageTranslations)
                            .pickBy(function (line) {
                                return line.key.toLowerCase().indexOf(that.searchPhrase.toLowerCase()) > -1;
                            })
                            .sortBy('value')
                            .value();
                }

                return _.sortBy(this.currentLanguageTranslations, 'value');
            },


            /**
             * List of translation lines from the current language.
             */
            currentLanguageTranslations: function () {
                return _.map(this.translations[this.selectedLanguage], function (value, key) {
                    return {key: key, value: value ? value : ''};
                });
            },


            /**
             * List of untranslated keys from the current language.
             */
            currentLanguageUntranslatedKeys: function () {
                return _.filter(this.translations[this.selectedLanguage], function (value) {
                    return !value;
                });
            }
        },


        methods: {
            /**
             * Add a new translation key.
             */
            addNewKey: function () {
                var that = this,
                        key = prompt("Please enter the new key");

                if (key != null) {
                    _.forEach(this.languages, function (lang) {
                        that.$set(that.translations[lang], key, '');
                    });
                }
            },


            /**
             * Remove the given key from all languages.
             */
            removeKey: function (key) {
                var that = this;

                if (confirm('Are you sure you want to remove "' + key + '"')) {
                    _.forEach(this.languages, function (lang) {
                        that.translations[lang] = _.omit(that.translations[lang], [key]);
                    });

                    this.selectedKey = null;
                }
            },


            /**
             * Save the translation lines.
             */
            save: function () {
                $.ajax('/langman/save', {
                    data: JSON.stringify({translations: this.translations}),
                    headers: {"X-CSRF-TOKEN": "{{csrf_token()}}"},
                    type: 'POST', contentType: 'application/json'
                }).done(function () {
                    alert('Saved Successfully.');
                })
            },


            /**
             * Collect untranslated strings from project files.
             */
            sync: function () {
                var that = this;

                $.post('/langman/sync', {_token: "{{csrf_token()}}"})
                        .done(function (response) {
                            that.translations = response.translations;

                            alert('Langman searched your files & found new keys to translate.');
                        })
            }
        }
    })
</script>
</body>
</html>
