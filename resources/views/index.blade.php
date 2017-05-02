<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Langman</title>

    <!-- Style sheets-->
    <link href='{{asset('vendor/langman/langman.css')}}' rel='stylesheet' type='text/css'>

    <!-- Icons -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
</head>
<body>
<div id="app" v-cloak>

    <nav class="navbar navbar-toggleable-md navbar-light mb-4">
        <div class="container">

            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <span class="navbar-text">@{{ _.toArray(currentLanguageTranslations).length }} Keys</span>
                </li>
                <li class="nav-item active ml-3" v-if="_.toArray(currentLanguageUntranslatedKeys).length">
                    <span class="navbar-text text-danger">@{{ _.toArray(currentLanguageUntranslatedKeys).length }} Un-translated</span>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto mr-3">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @{{ selectedLanguage }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a v-for="lang in languages"
                           href="#" role="button"
                           v-on:click="selectedLanguage = lang"
                           class="dropdown-item" href="#">@{{ lang }}</a>
                    </div>
                </li>
            </ul>
            <button class="btn btn-outline-info btn-sm mr-2"
                    v-on:click="promptToAddNewKey" v-if="languages.length"
                    type="button">Add
            </button>
            <button class="btn btn-outline-info btn-sm mr-2"
                    v-on:click="scanForKeys" v-if="languages.length"
                    type="button">Scan
            </button>
            <button class="btn btn-outline-success btn-sm"
                    v-on:click="save" v-if="languages.length"
                    type="button">Save
                <small v-if="this.hasChanges" class="text-danger">&#9679;</small>
            </button>
        </div>
    </nav>

    <div class="container">
        <div class="row" v-if="baseLanguage && _.toArray(currentLanguageTranslations).length">
            <div class="col">
                <div class="input-group mainSearch">
                    <div class="input-group-addon"><i class="fa fa-search"></i></div>
                    <input type="text" class="form-control" v-model="searchPhrase" placeholder="Search">
                </div>

                <div class="mt-4" style="overflow: scroll; height: 500px">
                    <div class="list-group">

                        <a href="#" role="button"
                           v-for="line in filteredTranslations"
                           v-on:click="selectedKey = line.key"
                           :class="['list-group-item', 'list-group-item-action', {'list-group-item-danger': !line.value}]">
                            <div class="d-flex w-100 justify-content-between">
                                <strong class="mb-1">@{{ line.key }}</strong>
                            </div>
                            <small class="text-muted">@{{ line.value }}</small>
                        </a>

                    </div>
                </div>
            </div>
            <div class="col">
                <div v-if="selectedKey">
                    <p class="mb-4">
                        @{{ selectedKey }}
                    </p>

                <textarea name="" rows="10" class="form-control mb-4"
                          v-model="translations[selectedLanguage][selectedKey]"
                          placeholder="Translate..."></textarea>

                    <div class="d-flex justify-content-center">
                        <button class="btn btn-outline-danger btn-sm" v-on:click="removeKey(selectedKey)">Delete this key</button>
                    </div>
                </div>

                <h5 class="text-muted text-center" v-else>
                    .<br>
                    .<br>
                    .<br><br>
                    Select a key from the list to the left
                </h5>
            </div>
        </div>

        <div v-else>
            <p class="lead text-center" v-if="!languages.length">
                There are no JSON language files in your project.<br>
                <button class="btn btn-outline-primary mt-3" v-on:click="addLanguage">Add Language</button>
            </p>

            <p class="lead text-center" v-if="languages.length">
                There are no Translation lines yet, start by adding new keys or <br>
                <a href="#" role="button" v-on:click="scanForKeys">scan</a> your project for lines to translate.
            </p>
        </div>
    </div>
</div>

<script>
    const langman = {
        csrf: "{{csrf_token()}}",
        baseLanguage: '{!! config('langmanGUI.base_language') !!}',
        languages: {!! json_encode($languages) !!},
        translations: {!! json_encode($translations) !!}
    };
</script>
<script src="{{asset('vendor/langman/langman.js')}}"></script>
</body>
</html>