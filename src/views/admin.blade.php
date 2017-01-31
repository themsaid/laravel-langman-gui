<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Langman GUI</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' rel='stylesheet' type='text/css'>

    <!-- JavaScript -->
    <script
            src="https://code.jquery.com/jquery-3.1.1.min.js"
            integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <style>
        body {
            background-color: #f2f7f9;
        }

        .langCheckboxes label {
            font-weight: normal;
            margin-left: 15px;
        }
    </style>
</head>
<body>
<div id="app" v-cloak>

    <div class="container">
        <h3 class="text-center">
            Translation Manager
        </h3>

        <div class="row">
            <div class="col-xs-6"></div>
            <div class="col-xs-6 text-right">
                <button class="btn btn-info" @click="sync">Sync</button>
                <button class="btn btn-primary" @click="save">Save</button>
            </div>
        </div>


        <div class="panel" style="margin-top:30px">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Keys</th>
                    <th v-for="lang in languages">@{{ lang }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(value, key) in translations.{{config('langmanGUI.base_language')}}">
                    <td>@{{ key }}</td>
                    <td v-for="lang in languages" :class="{'bg-danger': !translations[lang][key]}">
                        <input type="text" v-model="translations[lang][key]" class="form-control">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            languages: <?php echo json_encode($languages); ?>,
            translations: <?php echo json_encode($translations); ?>,
        },

        mounted: function () {
            var that = this;
            setInterval(function () {
                that.save();
            }, 60000);
        },

        methods: {
            /**
             * Save the translation lines.
             */
            save: function () {
                $.ajax('/langman/save',
                        {
                            data: JSON.stringify({translations: this.translations}),
                            headers: {"X-CSRF-TOKEN": "{{csrf_token()}}"},
                            type: 'POST', contentType: 'application/json'
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
                        })
            }
        }
    })
</script>
</body>
</html>
