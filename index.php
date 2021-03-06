<!DOCTYPE html>
<html>
<head>
    <title>Blockbench</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#3e90ff">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon" />
    <link rel="stylesheet" href="css/w3.css">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/spectrum.css">
    <link rel="stylesheet" href="css/style.css">
    <style type="text/css" id="bbstyle"></style>
</head>
<body spellcheck="false">
	<script>if (typeof module === 'object') {window.module = module; module = undefined;}//jQuery Fix</script>
        <script src="js/vue.min.js"></script>
        <script src="js/tree.vue.js"></script>
        <script src="js/jquery.js"></script>
        <script src="js/jquery-ui.min.js"></script>
        <script src="js/jimp.min.js"></script>
        <script src="js/spectrum.js"></script>
        <script src="js/three.js"></script>
        <script src="js/OrbitControls.js"></script>
        <script src="js/TransformControls.js"></script>
        <script src="js/OBJExporter.js"></script>
        
        <script src="js/util.js"></script>
        <script src="js/canvas.js"></script>
        <script src="js/settings.js"></script>
        <script src="js/blockbench.js"></script>

        <script type="text/javascript">
            if (typeof require == 'undefined') {
                document.write("<script type='application/x-suppress'>");
                isApp = false;
            }
        </script>
        <script type="text/javascript" src="js/app.js"></script>
        <script type="text/javascript">
            if (typeof require != 'undefined') {
                document.write("<script type='application/x-suppress'>");
            }
        </script>
        <script type="text/javascript" src="js/web.js"></script>

        <script src="js/io.js"></script>
        <script src="js/elements.js"></script>
        <script src="js/transform.js"></script>
        <script src="js/textures.js"></script>
        <script src="js/uv.js"></script>
        <script src="js/interface.js"></script>
        <script src="js/tools.js"></script>
        <script src="js/painter.js"></script>
        <script src="js/display.js"></script>
        <script src="js/extrude.js"></script>
        <script src="js/api.js"></script>
        <script src="js/plugin_loader.js"></script>
    	<script>if (window.module) module = window.module;</script>

    <div id="post_model" class="web_only post_data" hidden><?php
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $model = $_POST['model'];
            if ($model != "text") {
                echo $model;
            }
        }
    ?></div>
    <div id="post_textures" class="web_only post_data" hidden><?php
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $textures = $_POST['textures'];
            if ($textures != "text") {
                echo $textures;
            }
        }
    ?></div>
    <div style="display: none;"></div>
    <!---->
    <div id="blackout" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"></div>

    <div class="dialog draggable" id="welcome_screen">
        <div id="welcome_content"></div>
        <button type="button" class="large cancel_btn hidden" onclick="hideDialog()">Cancel</button>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="file_loader">
        <h2 class="dialog_handle">File Loader</h2>
        <h1></h1>

        <div class="dialog_bar">
            <input type="file" accept=".png" id="file_upload" class="hidden">
            <label for="file_upload" id="file_upload_label"><i class="material-icons">file_upload</i>Choose a file</label>
        </div>

        <div id="file_loader_meta">

            <div class="dialog_bar narrow">
                <label for="file_name">Name</label>
            </div>

            <div class="dialog_bar">
                <input type="text" class="input_wide" id="file_name">
            </div>

            <div class="dialog_bar narrow">
                <label for="file_folder">Subfolder</label>
            </div>

            <div class="dialog_bar">
                <input type="text" class="input_wide" id="file_folder">
            </div>
        </div>

        <div class="dialog_bar">
            <button type="button" id="web_import_btn" class="large confirm_btn">Import</button>
            <button type="button" class="large cancel_btn" onclick="hideDialog()">Cancel</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="updater">
        <h2 class="dialog_handle">Updates</h2>
        <h1></h1>

        <div id="updater_content"></div>


        <div class="progress_bar" id="update_bar">
            <div class="progress_bar_inner"></div>
        </div>
        <div class="dialog_bar">
            <button type="button" class="large cancel_btn confirm_btn uc_btn" onclick="hideDialog()">Close</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="plugins">
        <h2 class="dialog_handle">Plugins</h2>

        <div class="bar next_to_title">
            <div class="tool" onclick="loadPluginFromFile()"><i class="material-icons">folder</i><div class="tooltip">Load Plugin from File</div></div>
        </div>

        <div class="bar">
            <div class="tab open" onclick="switchPluginTabs(true)" id="installed_plugins">Installed</div>
            <div class="tab" onclick="switchPluginTabs(false)" id="all_plugins">Available</div>
            <div class="search_bar">
                <input type="text" class="dark_bordered" id="plugin_search_bar" oninput="Plugins.updateSearch()">
                <i class="material-icons" id="plugin_search_bar_icon">search</i>
            </div>
        </div>
        <ul class="list" id="plugin_list">
            <li v-for="plugin in installedPlugins" v-bind:plugin="plugin.id" v-bind:class="{testing: plugin.fromFile}">
                <div class="title">
                    <i v-if="plugin.icon.substr(0,3) !== 'fa-' " class="material-icons">{{ plugin.icon }}</i>
                    <i v-else class="fa fa_big" v-bind:class="plugin.icon"></i>
                {{ plugin.title }}</div>
                <div class="button_bar" v-if="checkIfInstallable(plugin) === true">
                    <button type="button" v-on:click="uninstall($event)" v-if="plugin.installed"><i class="material-icons">delete</i>Uninstall</button>
                    <button type="button" v-on:click="install($event)" v-else><i class="material-icons">add</i>Install</button>
                    <button type="button" class="local_only" v-on:click="plugin.reload()" v-if="plugin.installed && plugin.fromFile && isApp"><i class="material-icons">refresh</i>Reload</button>
                </div>
                <div class="button_bar tiny" v-else-if="checkIfInstallable(plugin) === 'outdated_client'">Requires the latest version<br>of Blockbench</div>
                <div class="button_bar tiny" v-else>Only for {{ plugin.variant }}</div>
                <div class="author">by {{ plugin.author }}</div>
                <div class="description">{{ plugin.description }}</div>
            </li>
            <div class="no_plugin_message" v-if="installedPlugins.length < 1 && showAll === false">No plugins are installed</div>
            <div class="no_plugin_message" v-if="installedPlugins.length < 1 && showAll === true" id="plugin_available_empty">No Plugins available</div>
        </ul>

        <div class="dialog_bar">
            <button type="button" class="large cancel_btn confirm_btn uc_btn" onclick="saveInstalledPlugins()">Close</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="update_notification">
        <h2 class="dialog_handle">An Update Is Available (<span></span>)</h2>
        <h1></h1>
        <div class="dialog_bar">
            <button type="button" class="large confirm_btn uc_btn" onclick="checkForUpdates(true)">Install</button>
            <button type="button" class="large cancel_btn uc_btn" onclick="hideDialog()">Later</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="entity_import">
        <h2 class="dialog_handle">Import Entity Model</h2>
        <div class="dialog_bar narrow">Select the model you want to import</div>
            <div class="search_bar">
                <input type="text" class="dark_bordered" id="pe_search_bar" oninput="pe_list._data.search_text = $(this).val().toUpperCase()">
                <i class="material-icons" id="plugin_search_bar_icon">search</i>
            </div>
        <ul id="pe_list" class="list">
            <li v-for="item in searched" v-bind:class="{ selected: item.selected }" v-on:click="selectE(item, $event)" ondblclick="loadPEModel()">
                <h4>{{ item.name }}</h4>
                <p>{{ item.bonecount }} Bones</p>
            </li>
        </ul>

        <div class="dialog_bar">
            <button type="button" class="large confirm_btn" onclick="loadPEModel()">Import</button>
            <button type="button" class="large cancel_btn" onclick="hideDialog()">Cancel</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="image_extruder">
        <h2 class="dialog_handle">Image Extrusion</h2>
        <h1></h1>

        <div class="dialog_bar">
            <label>Scan Mode</label>
            <select class="tool" id="scan_mode" name="scan_mode">
                <option id="areas" selected>Areas</option>
                <option id="lines">Lines</option>
                <option id="columns">Columns</option>
                <option id="pixels">Pixels</option>
            </select>
        </div>

        <div class="dialog_bar">
            <label>Pixel Opacity Tolerance</label>
            <input class="tool" type="range" id="scan_tolerance" value="255" min="1" max="255">
            <label id="scan_tolerance_label">255</label>
        </div>

        <canvas height="256" width="256" id="extrusion_canvas"></canvas>

        <div class="progress_bar" id="extrusion_bar">
            <div class="progress_bar_inner"></div>
        </div>

        <div class="dialog_bar">
            <button type="button" class="large confirm_btn" onclick="convertExtrusionImage()">Scan and Import</button>
        </div>
    </div>

    <div class="dialog draggable paddinged" id="texture_edit">
        <h2 class="dialog_handle" id="te_title">Texture</h2>

        <div id="texture_menu_thumbnail"></div>

        <div class="bar">
            <input type="file" accept=".png" id="texture_change" class="hidden">
            <label for="texture_change" id="texture_change_label" class="web_only tool"><i class="material-icons">file_upload</i><div class="tooltip">Change File</div></label>
            <div id="change_file_button" class="local_only tool"><i class="material-icons">file_upload</i><div class="tooltip">Change File</div></div>
            <div class="tool link_only" onclick="textures.selected.refresh(true)"><i class="material-icons">refresh</i><div class="tooltip">Reload</div></div>
            <div class="tool link_only" onclick="textures.selected.openFolder()"><i class="material-icons">folder</i><div class="tooltip">Open Folder</div></div>
            <div class="tool" onclick="textures.selected.remove()"><i class="material-icons">delete</i><div class="tooltip">Delete</div></div>
        </div>

        <p class="multiline_text" id="te_path">path</p>

        <div class="dialog_bar narrow bitmap_only"><label>Name</label> </div>
        <div class="dialog_bar bitmap_only">
            <input type="text" class="input_wide" id="te_name">
        </div>

        <div class="dialog_bar narrow"><label>Variable</label> </div>
        <div class="dialog_bar">
            <input type="text" class="input_wide" id="te_variable">
        </div>

        <div class="dialog_bar narrow"><label>Folder</label> </div>
        <div class="dialog_bar">
            <input type="text" class="input_wide" id="te_folder">
        </div>

        <div class="dialog_bar">
            <button type="button" class="large confirm_btn cancel_btn" onclick="saveTextureMenu()">Close</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="scaling">
        <h2 class="dialog_handle">Scale Model</h2>

        <div class="dialog_bar narrow">
            <label for="file_folder">Axis</label>
        </div>

        <div class="dialog_bar" style="height: 32px;">
            <input type="checkbox" class="toggle_panel" id="model_scale_x_axis" onchange="scaleAll()" checked>
            <label class="toggle_panel" for="model_scale_x_axis">X</label>
            <input type="checkbox" class="toggle_panel" id="model_scale_y_axis" onchange="scaleAll()" checked>
            <label class="toggle_panel" for="model_scale_y_axis">Y</label>
            <input type="checkbox" class="toggle_panel" id="model_scale_z_axis" onchange="scaleAll()" checked>
            <label class="toggle_panel" for="model_scale_z_axis">Z</label>
        </div>

        <div class="dialog_bar narrow">
            <label for="file_folder">Scale</label>
        </div>

        <div class="dialog_bar" style="height: 32px;">
            <input type="range" id="model_scale_range" value="1" min="0" max="4" step="0.02" oninput="modelScaleSync()">
            <input type="number" class="f_left" id="model_scale_label" min="0" max="4" step="0.02" value="1" oninput="modelScaleSync(true)">
        </div>
        <div class="dialog_bar narrow" id="scaling_clipping_warning"></div>

        <div class="dialog_bar">
            <button type="button" onclick="scaleAll(true)" class="large confirm_btn">Scale</button>
            <button type="button" class="large cancel_btn" onclick="cancelScaleAll()">Cancel</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="create_preset">
        <h2 class="dialog_handle">Create Preset</h2>
        <div class="dialog_bar">Select the slots you want to save</div>

        <div class="dialog_bar">
            <input type="checkbox" id="thirdperson_righthand_save" checked>
            <label for="thirdperson_righthand_save">Thirdperson Right</label>
        </div>

        <div class="dialog_bar">
            <input type="checkbox" id="thirdperson_lefthand_save" checked>
            <label for="thirdperson_lefthand_save">Thirdperson Left</label>
        </div>

        <div class="dialog_bar">
            <input type="checkbox" id="firstperson_righthand_save" checked>
            <label for="firstperson_righthand_save">Firstperson Right</label>
        </div>

        <div class="dialog_bar">
            <input type="checkbox" id="firstperson_lefthand_save" checked>
            <label for="firstperson_lefthand_save">Firstperson Left</label>
        </div>

        <div class="dialog_bar">
            <input type="checkbox" id="head_save" checked>
            <label for="head_save"">Head</label>
        </div>

        <div class="dialog_bar">
            <input type="checkbox" id="gui_save" checked>
            <label for="gui_save">GUI</label>
        </div>

        <div class="dialog_bar">
            <input type="checkbox" id="ground_save" checked>
            <label for="ground_save">Ground</label>
        </div>

        <div class="dialog_bar">
            <input type="checkbox" id="fixed_save" checked>
            <label for="fixed_save">Frame</label>
        </div>

        <div class="dialog_bar narrow">
            <label>Name</label>
        </div>

        <div class="dialog_bar">
            <input type="text" id="preset_name" class="input_wide" id="new preset">
        </div>

        <div class="dialog_bar">
            <button type="button" class="large confirm_btn" onclick="createPreset()">Create</button>
            <button type="button" class="large cancel_btn" onclick="hideDialog()">Cancel</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="selection_creator">
        <h2 class="dialog_handle">Select</h2>

        <div class="dialog_bar">
            <input type="checkbox" id="selgen_new" checked>
            <label class="name_space_left" for="selgen_new">New Selection</label>
        </div>

        <div class="dialog_bar">
            <input type="checkbox" id="selgen_group">
            <label class="name_space_left" for="selgen_group">Only In This Group</label>
        </div>

        <div class="dialog_bar">
            <label class="name_space_left" for="selgen_new">Name Contains</label>
            <input type="text" class="dark_bordered half" id="selgen_name">
        </div>

        <div class="dialog_bar">
            <label class="name_space_left" for="selgen_new">Random</label>
            <input type="range" min="0" max="100" step="1" value="100" class="tool half" id="selgen_random">
        </div>

        <div class="dialog_bar">
            <button type="button" class="large confirm_btn" onclick="createSelection()">Select</button>
            <button type="button" class="large cancel_btn" onclick="hideDialog()">Cancel</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="project_settings">
        <h2 class="dialog_handle">Project</h2>

        <div class="dialog_bar narrow">
            <label for="project_name">File Name</label>
        </div>
        <div class="dialog_bar">
            <input v-model="Project.name" type="text" id="project_name" class="dark_bordered input_wide">
        </div>


        <div class="dialog_bar narrow">
            <label for="project_parent">Parent Model</label>
        </div>
        <div class="dialog_bar">
            <input v-model="Project.parent" type="text" id="project_parent" class="dark_bordered input_wide">
        </div>


        <div class="dialog_bar narrow block_mode_only">
            <label for="project_description">Description Tag</label>
        </div>
        <div class="dialog_bar block_mode_only">
            <input v-model="Project.description" type="text" id="project_description" class="dark_bordered input_wide">
        </div>


        <div class="dialog_bar" class="name_space_left block_mode_only">
            <input v-model="Project.ambientocclusion" type="checkbox" id="project_ambientocclusion">
            <label for="project_description" class="name_space_left">Ambient Occlusion</label>
        </div>


        <div class="dialog_bar narrow">
            <label for="project_description">Texture Size</label>
        </div>
        <div class="dialog_bar">
            <label for="project_texsize_x" class="inline_label">Width</label>
            <input v-model="Project.texture_width" type="number" id="project_texsize_x" class="dark_bordered mediun_width" min="1" value="64">
            <label for="project_texsize_y" class="inline_label">Height</label>
            <input v-model="Project.texture_height" type="number" id="project_texsize_y" class="dark_bordered mediun_width" min="1" value="32">
        </div>


        <div class="dialog_bar">
            <button type="button" class="large confirm_btn cancel_btn" onclick="saveProjectSettings()">Close</button>
            <button type="button" class="large" id="entity_mode_convert" onclick="entityMode.convert()">To Entity Model</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog" id="settings">
        <div class="dialog_bar borderless">
            <div class="tab open" id="setting" onclick="setSettingsTab('setting')">Settings</div>
            <div class="tab" id="keybindings" onclick="setSettingsTab('keybindings')">Keybindings</div>
            <div class="tab" id="layout_settings" onclick="setSettingsTab('layout_settings')">Layout</div>
            <div class="tab" id="credits" onclick="setSettingsTab('credits')">About</div>
        </div>
        <div id="setting" class="tab_content">
            <h2>Settings</h2>
            <ul id="settingslist">
                <li v-for="setting in settings">

                    <template v-if="setting.hidden && isApp">
                        <i id="default_path_icon" class="material-icons" onclick="openDefaultTexturePath()">burst_mode</i>
                        <div>
                            <div class="setting_name">Default Texture Path</div>
                            <div class="setting_description">Blockbench picks textures from there if it can't find them</div>
                        </div>
                    </template>

                    <template v-else-if="setting.is_title">
                        <h3>{{ setting.title }}</h3>
                    </template>

                    <template v-else-if="setting.is_string">
                        <input type="text" class="dark_bordered" style="width: 96%" v-model="setting.value" v-on:input="saveSettings()">
                    </template>

                    <template v-else>

                        <template v-if="setting.is_number">
                            <input type="number" v-model="setting.value" v-on:input="saveSettings()">
                        </template>

                        <template v-else>
                            <input type="checkbox" v-model="setting.value" v-on:click="saveSettings()">
                        </template>

                        <div>
                            <div class="setting_name">{{ setting.name }}</div>
                            <div class="setting_description">{{ setting.desc }}</div>
                        </div>
                    </template>
                </li>
            </ul>
        </div>
        <div id="keybindings" class="hidden tab_content">
            <h2>Keybindings</h2>
            <div class="bar next_to_title">
                <div class="tool" onclick="resetAllKeybindings()"><i class="material-icons">replay</i><div class="tooltip">Reset</div></div>
            </div>
            <ul id="keybindlist">
                <li v-for="key in keybinds">
                    <template v-if="key.is_title">
                        <h3>{{ key.title }}</h3>
                    </template>
                    <template v-else>
                        <div>{{ key.name }}</div>
                        <div class="keybindslot" contenteditable="true" v-on:click.stop="prepareInput(key)">{{ key.char }}</div>
                        <div class="tool" v-on:click="resetKey(key)"><i class="material-icons">replay</i></div>
                    </template>
                </li>
            </ul>
        </div>
        <div id="layout_settings" class="hidden tab_content">
            <h2>Layout</h2>
            <div class="bar next_to_title">
                <div class="tool" onclick="importLayout()"><i class="material-icons">folder</i><div class="tooltip">Import Layout</div></div>
                <div class="tool" onclick="exportLayout()"><i class="material-icons">style </i><div class="tooltip">Export Layout</div></div>
                <div class="tool" onclick="colorSettingsSetup(true)"><i class="material-icons">replay</i><div class="tooltip">Reset Layout</div></div>
            </div>
            <div id="color_wrapper">
                <div class="color_field">
                    <input type="color" class="color_input" id="color_back" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_back" style="background-color: var(--color-back)" class="color_input"></label>
                    <div class="desc">
                        <h4>Back</h4>
                        <p>Backgrounds and Input fields</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_dark" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_dark" style="background-color: var(--color-dark)" class="color_input"></label>
                    <div class="desc">
                        <h4>Dark</h4>
                        <p>Canvas background</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_ui" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_ui" style="background-color: var(--color-ui)" class="color_input"></label>
                    <div class="desc">
                        <h4>UI</h4>
                        <p>Main interface color</p>
                    </div>
                </div>
                <!--Button-->
                <div class="color_field">
                    <input type="color" class="color_input" id="color_button" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_button" style="background-color: var(--color-button)" class="color_input"></label>
                    <div class="desc">
                        <h4>Button</h4>
                        <p>Buttons and switches</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_hover" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_hover" style="background-color: var(--color-hover)" class="color_input"></label>
                    <div class="desc">
                        <h4>Hover</h4>
                        <p>Selected tabs and objects</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_border" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_border" style="background-color: var(--color-border)" class="color_input"></label>
                    <div class="desc">
                        <h4>Border</h4>
                        <p>Border of buttons and inputs</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_accent" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_accent" style="background-color: var(--color-accent)" class="color_input"></label>
                    <div class="desc">
                        <h4>Accent</h4>
                        <p>Slider thumb and other details</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_grid" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_grid" style="background-color: var(--color-grid)" class="color_input"></label>
                    <div class="desc">
                        <h4>Grid</h4>
                        <p>3D preview grid</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_text" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_text" style="background-color: var(--color-text)" class="color_input"></label>
                    <div class="desc">
                        <h4>Text</h4>
                        <p>Normal text</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_light" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_light" style="background-color: var(--color-light)" class="color_input"></label>
                    <div class="desc">
                        <h4>Light</h4>
                        <p>Selected text</p>
                    </div>
                </div>
                <div class="color_field">
                    <input type="color" class="color_input" id="color_text_acc" oninput="changeUIColor(event)" onclick="initUIColor(event)">
                    <label for="color_text_acc" style="background-color: var(--color-text_acc)" class="color_input"></label>
                    <div class="desc">
                        <h4>Accent Text</h4>
                        <p>Text on accent elements</p>
                    </div>
                </div>
                <div class="color_field">
                </div>
            </div>

            <div class="dialog_bar">
                <label class="name_space_left" for="layout_font_main">Main Font</label>
                <input type="text" class="half dark_bordered" id="layout_font_main" oninput="changeUIFont('main')">
            </div>

            <div class="dialog_bar">
                <label class="name_space_left" for="layout_font_headline">Headline Font</label>
                <input type="text" class="half dark_bordered" id="layout_font_headline" oninput="changeUIFont('headline')">
            </div>

        </div>
        <div id="credits" class="hidden tab_content">
            <h2>About</h2>
            <p><b>Version: </b><span id="version_tag"><script>
                $('#version_tag').text(appVersion)
            </script></span></p>
            <p><b>Creator: </b>JannisX11</p>
            <p><b>Website: </b><a class="open-in-browser" href="http://blockbench.net">blockbench.net</a></p>
            <p><b>Bug Tracker: </b><a class="open-in-browser" href="https://github.com/JannisX11/blockbench/issues">github.com/JannisX11/blockbench</a></p>
            <p class="local_only">This app is built with <b>Electron</b>, a framework for creating native applications with web technologies like JavaScript, HTML, and CSS.</p>
            <a class="open-in-browser local_only" href="https://electron.atom.io">electron.atom.io</a>
            <p>Vertex Snapping is based on a design by SirBenet</p>
            <p><b>Icon Packs: </b><a href="https://material.io/icons/" class="open-in-browser">material.io/icons</a> &amp; <a href="http://fontawesome.io/icons/" class="open-in-browser">fontawesome</a></p>
            <p><b>Javascript Libraries: </b>
                <a href="https://jquery.com" class="open-in-browser">jQuery</a>
                <a href="https://jqueryui.com" class="open-in-browser">jQuery UI</a>
                <a href="https://vuejs.org" class="open-in-browser">VueJS</a>
                <a href="https://github.com/weibangtuo/vue-tree" class="open-in-browser">Vue Tree</a>
                <a href="https://threejs.org" class="open-in-browser">ThreeJS</a>
                <a href="https://github.com/lydell/json-stringify-pretty-compact" class="open-in-browser">json-stringify-pretty-compact</a>
                <a href="https://github.com/oliver-moran/jimp" class="open-in-browser">Jimp</a>
                <a href="https://bgrins.github.io/spectrum" class="open-in-browser">Spectrum</a>
            </p>
        </div>
        <div class="dialog_bar">
            <button type="button" class="large confirm_btn cancel_btn" onclick="saveSettings()">Close</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable" id="uv_dialog">
        <div class="dialog_bar borderless dialog_handle block_mode_only" id="uv_tab_bar">
            <div onclick="uv_dialog.openTab('all')" id="all" class="tab open">All</div>
            <div onclick="uv_dialog.openTab('north')" id="north" class="tab">North</div>
            <div onclick="uv_dialog.openTab('south')" id="south" class="tab">South</div>
            <div onclick="uv_dialog.openTab('west')" id="west" class="tab">West</div>
            <div onclick="uv_dialog.openTab('east')" id="east" class="tab">East</div>
            <div onclick="uv_dialog.openTab('up')" id="up" class="tab">Up</div>
            <div onclick="uv_dialog.openTab('down')" id="down" class="tab">Down</div>
        </div>
        <h2 class="dialog_handle entity_mode_only">UV Editor</h2>
        <div id="uv_dialog_all" class="uv_dialog_content uv_dialog_all_only">
            
        </div>
        <div id="uv_dialog_single" class="uv_dialog_content">
            
        </div>
        <div class="bar block_mode_only" id="uv_dialog_toolbar">                
            <select class="tool" id="uv_snap" name="grid_snap" style="width: 50px;" onchange="uv_dialog.changeGrid()">
                <option id="auto" selected>Auto</option>
                <option id="16">16x16</option>
                <option id="32">32x32</option>
                <option id="64">64x64</option>
                <option id="none">Free</option>
            </select>
                <div class="toolbar_seperator"></div>
            <div class="uv_dialog_all_only">
                <div class="tool" onclick="uv_dialog.selectAll()"><i class="material-icons">view_module</i><div class="tooltip">Select All</div></div>
                <div class="tool" onclick="uv_dialog.selectNone()"><i class="material-icons">clear</i><div class="tooltip">Deselect</div></div>
                    <div class="toolbar_seperator"></div>
            </div>
            <div class="tool" onclick="uv_dialog.forSelection('maximize')"><i class="material-icons">zoom_out_map</i><div class="tooltip">Maximize</div></div>
            <div class="tool" onclick="uv_dialog.forSelection('setAutoSize')"><i class="material-icons">brightness_auto</i><div class="tooltip">Auto Size</div></div>
            <div class="tool" onclick="uv_dialog.forSelection('setRelativeAutoSize')"><i class="material-icons">brightness_auto</i><div class="tooltip">Relative Auto Size</div></div>
                <div class="toolbar_seperator"></div>
            <div class="tool" onclick="uv_dialog.forSelection('mirrorX')"><i class="material-icons">flip</i><div class="tooltip">Mirror X</div></div>
            <div class="tool" onclick="uv_dialog.forSelection('mirrorY')"><i class="material-icons" style="transform: rotate(90deg)">flip</i><div class="tooltip">Mirror Y</div></div>
                <div class="toolbar_seperator"></div>
            <div class="tool" onclick="uv_dialog.copy()"><i class="material-icons">content_copy</i><div class="tooltip">Copy</div></div>
            <div class="tool" onclick="uv_dialog.paste()"><i class="material-icons">content_paste</i><div class="tooltip">Paste</div></div>
                <div class="toolbar_seperator"></div>
            <div class="tool" onclick="uv_dialog.forSelection('clear')"><i class="material-icons">clear</i><div class="tooltip">Clear</div></div>
            <div class="tool" onclick="uv_dialog.forSelection('reset')"><i class="material-icons">replay</i><div class="tooltip">Reset</div></div>
                <div class="toolbar_seperator"></div>
            <label for="tint" class="text_padding toolbar_label">Tint</label>
            <input type="checkbox" id="tint" onchange="uv_dialog.forSelection('switchTint')" class="text_padding">
                <div class="toolbar_seperator"></div>
            <label class="text_padding toolbar_label">Cullface</label>
            <select class="tool" id="cullface" onchange="uv_dialog.forSelection('switchCullface')" style="width: 72px;">
                <option class="" value="off" id="off">None</option>
                <option class="" value="north" id="north">North</option>
                <option class="" value="south" id="south">South</option>
                <option class="" value="west" id="west">West</option>
                <option class="" value="east" id="east">East</option>
                <option class="" value="up" id="up">Up</option>
                <option class="" value="down" id="down">Down</option>
            </select>
            <div class="tool" onclick="uv_dialog.forSelection('autoCullface')"> <i class="material-icons">block</i><div class="tooltip">Auto Cullface</div></div>
                <div class="toolbar_seperator"></div>
            <input class="tool" id="uv_rotation" onchange="uv_dialog.forSelection('rotate')" title="Rotation" type="range" min="0" max="270" step="90" value="0">

        </div>
        <button type="button" onclick="hideDialog()" class="large confirm_btn cancel_btn hidden">Close</button>
        
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div class="dialog draggable paddinged" id="text_input">
        <h2 class="dialog_handle">Input</h2>

        <div class="dialog_bar">
            <input type="text" id="text_input_field" class="dark_bordered input_wide">
        </div>

        <div class="dialog_bar">
            <button type="button" class="large confirm_btn" onclick="hideDialog()">Confirm</button>
            <button type="button" class="large cancel_btn" onclick="hideDialog()">Cancel</button>
        </div>
        <div id="dialog_close_button" onclick="$('.dialog#'+open_dialog).find('.cancel_btn:not([disabled])').click()"><i class="material-icons">clear</i></div>
    </div>

    <div id="plugin_dialog_wrapper"></div>

    <header>
        <ul>
            <div id="title">Blockbench</div>
            <li class="context_handler menu_bar_point">
                <div>File</div>
                <ul class="dropdown" id="file_menu_list">
                    <li onclick="showDialog('project_settings');"><i class="material-icons">featured_play_list</i>Project...</li>
                    <li class="menu_seperator"></li>
                    <li><i class="material-icons">insert_drive_file</i>New
                        <i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="newProject()"><i class="material-icons">insert_drive_file</i>Model</li>
                            <li onclick="newProject(true)"><i class="material-icons">pets</i>Entity Model</li>
                        </ul>
                    </li>
                    <li class="local_only"><i class="material-icons">history</i>Recent
                        <i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2" id="recent_projects"></ul>
                    </li>
                    <li><i class="material-icons">folder_open</i>Open
                        <i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="openFile(true)"><i class="material-icons">assessment</i>Model</li>
                            <li onclick="importExtrusion(true)"><i class="material-icons">eject</i>Extruded Texture</li>
                        </ul>
                    </li>
                    <li><i class="material-icons">note_add</i>Add
                        <i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="openFile(false)"><i class="material-icons">assessment</i>Model</li>
                            <li onclick="importExtrusion(false)"><i class="material-icons">eject</i>Extruded Texture</li>
                        </ul>
                    </li>
                    <li><i class="material-icons">file_download</i>Export<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li class="local_only block_mode_only" onclick="saveFileBlock()"><i class="material-icons">insert_drive_file</i>Blockmodel</li>
                            <li class="web_only block_mode_only" onclick="saveFileBlock()"><i class="material-icons">file_download</i>Blockmodel</li>
                            <li onclick="saveFileEntity()" class="entity_mode_only"><i class="material-icons">pets</i>Bedrock Entity</li>
                            <li onclick="saveFileOptifine()" class="block_mode_only"><i class="material-icons">play_circle_outline</i>Optifine Entity</li>
                            <li onclick="saveFileObj()"><i class="material-icons">crop_square</i>OBJ Model</li>
                        </ul>
                    </li>
                    <li class="web_only" onclick="saveFileBlock()"><i class="material-icons">file_download</i>Download</li>
                    <li class="local_only" onclick="saveFile()"><i class="material-icons">save</i>Save</li>
                    <li class="menu_seperator"></li>
                    <li onclick="openSettings()"><i class="material-icons">settings</i>Settings...</li>
                    <li onclick="showDialog('plugins')"><i class="material-icons">extension</i>Plugins...</li>
                    <li class="local_only" id="app_update_button" onclick="checkForUpdates()"><i class="material-icons">update</i>Updates...</li>
                    <li onclick="randomHelpMessage()"><i class="material-icons">help</i>Tip</li>
                    <li><i class="material-icons">loyalty</i><a class="open-in-browser" href="http://blockbench.net/donate.html">Donate</a></li>
                </ul>
            </li>
            <li class="m_edit context_handler menu_bar_point">
                <div>Edit</div>
                <ul class="dropdown">
                    <li onclick="Undo.undo()"><i class="material-icons">undo</i>Undo</li>
                    <li onclick="Undo.redo()"><i class="material-icons">redo</i>Redo</li>
                    <li class="menu_seperator"></li>
                    <li onclick="addCube()"><i class="material-icons">add_box</i>Add Cube</li>
                    <li onclick="duplicateCubes()"><i class="material-icons">content_copy</i>Duplicate</li>
                    <li onclick="deleteCubes()"><i class="material-icons">delete</i>Delete</li>
                    <li onclick="sortOutliner()"><i class="material-icons">sort_by_alpha</i>Sort Outliner</li>
                    <li class="menu_seperator"></li>
                    <li onclick="toggleSetting('move_origin')"><i class="material-icons settings_dependent" setting="move_origin">check_box</i>Move Relative</li>
                    <li class="menu_seperator"></li>
                    <li onclick="showDialog('selection_creator')"><i class="material-icons">filter_list</i>Select...</li>
                    <li onclick="invertSelection()"><i class="material-icons">swap_horiz</i>Invert Selection</li>
                    <li class="plugin_submenu_hide menu_seperator"></li>
                    <li class="plugin_submenu_hide"><i class="material-icons">extension</i>Plugin<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2" id="plugin_submenu"></ul>
                    </li>
                </ul>
            </li>
            <li class="m_edit context_handler menu_bar_point">
                <div>Transform</div>
                <ul class="dropdown">
                    <li onclick="openScaleAll()"><i class="material-icons">settings_overscan</i>Scale...</li>
                    <li onclick="showInflationDialog()" class="entity_mode_only"><i class="material-icons">settings_overscan</i>Inflate...</li>
                    <li><i class="material-icons">rotate_90_degrees_ccw</i>Rotate<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="rotateSelectedY(1)"><i class="material-icons color_y">rotate_right</i>Rotate CW</li>
                            <li onclick="rotateSelectedY(3)"><i class="material-icons color_y">rotate_left</i>Rotate Counter-CW</li>
                            <li onclick="rotateSelectedX(1)"><i class="material-icons color_x">rotate_right</i>Rotate CW</li>
                            <li onclick="rotateSelectedX(3)"><i class="material-icons color_x">rotate_left</i>Rotate Counter-CW</li>
                            <li onclick="rotateSelectedZ(1)"><i class="material-icons color_z">rotate_right</i>Rotate CW</li>
                            <li onclick="rotateSelectedZ(3)"><i class="material-icons color_z">rotate_left</i>Rotate Counter-CW</li>
                        </ul>
                    </li>
                    <li><i class="material-icons">flip</i>Flip<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="mirror(0)"><i class="material-icons color_x">flip</i>Flip X</li>
                            <li onclick="mirror(1)"><i class="material-icons color_y">flip</i>Flip Y</li>
                            <li onclick="mirror(2)"><i class="material-icons color_z">flip</i>Flip Z</li>
                        </ul>
                    </li>
                    <li><i class="material-icons">filter_center_focus</i>Center<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="centerCubes(0)"><i class="material-icons color_x">vertical_align_center</i>Center X</li>
                            <li onclick="centerCubes(1)"><i class="material-icons color_y">vertical_align_center</i>Center Y</li>
                            <li onclick="centerCubes(2)"><i class="material-icons color_z">vertical_align_center</i>Center Z</li>
                            <li onclick="centerCubesAll()"><i class="material-icons">filter_center_focus</i>Center All</li>
                        </ul>
                    </li>
                    <li><i class="material-icons">list</i>Properties<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="toggleCubeProperty('visibility')"><i class="material-icons">visibility</i>Visibility</li>
                            <li onclick="toggleCubeProperty('export')"><i class="material-icons">save</i>Export</li>
                            <li onclick="toggleCubeProperty('autouv')"><i class="material-icons">fullscreen_exit</i>Auto UV</li>
                            <li onclick="toggleCubeProperty('shade', true)"><i class="material-icons">wb_sunny</i>Shading</li>
                            <li onclick="renameCubes()"><i class="material-icons">text_format</i>Name</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="m_disp context_handler menu_bar_point">
                <div>Display</div>
                <ul class="dropdown">
                    <li onclick="showDialog('create_preset')"><i class="material-icons">add</i>New Preset</li>
                    <li onclick="copyDisplaySlot()"><i class="material-icons">content_copy</i>Copy</li>
                    <li onclick="pasteDisplaySlot()"><i class="material-icons">content_paste</i>Paste</li>
                    <li onclick="toggleSetting('display_grid')"><i class="material-icons settings_dependent" setting="display_grid">check_box</i>Show Grid</li>
                    <li onclick="exitDisplaySettings()"><i class="material-icons">mode_edit</i>Edit Mode</li>
                </ul>
            </li>
            <li class="context_handler menu_bar_point">
                <div>View</div>
                <ul class="dropdown">
                    <li class="local_only" onclick="currentwindow.setFullScreen(!currentwindow.isFullScreen())"><i class="material-icons">fullscreen</i>Fullscreen</li>
                    <li class="local_only"><i class="material-icons">search</i>Zoom<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="setZoomLevel('in')"><i class="material-icons">zoom_in</i>Zoom In</li>
                            <li onclick="setZoomLevel('out')"><i class="material-icons">zoom_out</i>Zoom Out</li>
                            <li onclick="setZoomLevel('reset')"><i class="material-icons">zoom_out_map</i>Reset Zoom</li>
                        </ul>
                    </li>
                    <li class="menu_seperator local_only"></li>
                    <li class="m_edit" onclick="setCameraType('pers')"><i class="material-icons">camera_alt</i>Normal View</li>
                    <li class="m_edit"><i class="material-icons">crop_original</i>Side View<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="setCameraType('ortho', 0)"><i class="material-icons color_y">label</i>Top</li>
                            <li onclick="setCameraType('ortho', 1)"><i class="material-icons color_y">label</i>Bottom</li>
                            <li onclick="setCameraType('ortho', 2)"><i class="material-icons color_z">label</i>South</li>
                            <li onclick="setCameraType('ortho', 3)"><i class="material-icons color_z">label</i>North</li>
                            <li onclick="setCameraType('ortho', 4)"><i class="material-icons color_x">label</i>East</li>
                            <li onclick="setCameraType('ortho', 5)"><i class="material-icons color_x">label</i>West</li>
                        </ul>
                    </li>
                    <li class="m_edit" onclick="resetCamera()"><i class="material-icons">replay</i>Reset Camera</li>
                    <li class="menu_seperator m_edit"></li>
                    <li onclick="toggleWireframe()"><i class="material-icons">border_clear</i>Wireframe</li>
                    <li><i class="material-icons">wallpaper</i>Background<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="loadBackgroundImage()"><i class="material-icons">wallpaper</i>Add Background</li>
                            <li onclick="scenesSetup(true)"><i class="material-icons">replay</i>Reset All</li>
                        </ul>
                    </li>
                    <li><i class="material-icons">camera_alt</i>Screenshot<i class="material-icons more_icon">navigate_next</i>
                        <ul class="dropdown level2">
                            <li onclick="Screencam.cleanCanvas()"><i class="fa fa_big fa-cubes"></i>Capture Model</li>
                            <li onclick="Screencam.normalCanvas()"><i class="material-icons">grid_on</i>Capture Canvas</li>
                            <li class="local_only" onclick="Screencam.fullScreen()"><i class="material-icons">computer</i>Capture Window</li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
        <div id="toolbox" class="f_left"></div>
        <div class="placeholder m_edit"></div>
        <div id="tool_options_transform" class="f_left tool_options">
            <div class="tool wide m_edit nslide_tool selection_only"><div class="nslide" n-action="pos_x"></div><div class="tooltip">Move X</div></div>
            <div class="tool wide m_edit nslide_tool selection_only"><div class="nslide" n-action="pos_y"></div><div class="tooltip">Move Y</div></div>
            <div class="tool wide m_edit nslide_tool selection_only"><div class="nslide" n-action="pos_z"></div><div class="tooltip">Move Z</div></div>
            <div class="placeholder m_edit selection_only"></div>
            <div class="tool wide m_edit nslide_tool selection_only"><div class="nslide" n-action="size_x"></div><div class="tooltip">Scale X</div></div>
            <div class="tool wide m_edit nslide_tool selection_only"><div class="nslide" n-action="size_y"></div><div class="tooltip">Scale Y</div></div>
            <div class="tool wide m_edit nslide_tool selection_only"><div class="nslide" n-action="size_z"></div><div class="tooltip">Scale Z</div></div>
        </div>
        <div id="tool_options_brush" class="f_left tool_options">
            <label class="f_left in_toolbar">Mode:</label>
            <select class="dark_bordered" id="brush_mode">
                <option id="round">Normal</option>
                <option id="noise">Noise</option>
                <option id="eraser">Eraser</option>
                <option id="fill">Fill</option>
            </select>
            <label class="f_left in_toolbar">Color:</label>
            <input class="f_left" type="text" id="brush_color">
            <label class="f_left in_toolbar">Size:</label>
            <input type="range" class="dark_bordered" id="brush_size" step="1" value="0" min="1" max="20" style="width: 80px;">
            <label class="f_left in_toolbar">Soft:</label>
            <input type="range" class="dark_bordered" id="brush_softness" step="0.1" value="0" min="0" max="1" style="width: 80px;">
        </div>
        <div id="tool_options_vertex_snap" class="f_left tool_options">
            <label class="f_left in_toolbar">Mode:</label>
            <select class="dark_bordered" id="vertex_scale">
                <option id="move">Move</option>
                <option id="scale">Scale</option>
            </select>
        </div>
        <dir class="mode_tab block_mode_only" id="mode_display_tab" onclick="if (!display_mode) {enterDisplaySettings()}">Display</dir>
        <dir class="mode_tab open" id="mode_edit_tab" onclick="if (display_mode) {exitDisplaySettings()}">Edit</dir>
    </header>
    <div id="left_bar" class="sidebar">
        <div id="uv" class="ui m_edit selection_only">
            <h3>UV</h3>
            <div class="bar next_to_title">
                <div class="tool block_mode_only" id="clear" onclick="uv_dialog.openAll()"><i class="material-icons">view_module</i><div class="tooltip">UV Window</div></div>
                <div class="tool" id="clear" onclick="uv_dialog.openFull()"><i class="material-icons">web_asset</i><div class="tooltip">Full View</div></div>
            </div>
            <div id="texture_bar" onclick="main_uv.loadSelectedFace()" class="bar tabs_small block_mode_only">

                <input type="radio" name="side" id="north_radio" checked>
                <label for="north_radio">North</label>

                <input type="radio" name="side" id="south_radio">
                <label for="south_radio">South</label>
                
                <input type="radio" name="side" id="west_radio">
                <label for="west_radio">West</label>

                <input type="radio" name="side" id="east_radio">
                <label for="east_radio">East</label>

                <input type="radio" name="side" id="up_radio">
                <label for="up_radio">Up</label>

                <input type="radio" name="side" id="down_radio">
                <label for="down_radio">Down</label>
            </div>
        </div>
        <div id="display_settings" class="ui m_disp">
            <h3>Display</h3>
            <p>Slot</p>
            <div id="display_bar" class="bar tabs_small">
                <input class="hidden" type="radio" name="display" id="thirdperson_righthand" checked>
                <label class="tool" for="thirdperson_righthand" onclick="loadDispThirdRight()"><i class="material-icons">accessibility</i><div class="tooltip">Thirdperson Right</div></label>
                <input class="hidden" type="radio" name="display" id="thirdperson_lefthand">
                <label class="tool" for="thirdperson_lefthand" onclick="loadDispThirdLeft()"><i class="material-icons">accessibility</i><div class="tooltip">Thirdperson Left</div></label>

                <input class="hidden" type="radio" name="display" id="firstperson_righthand">
                <label class="tool" for="firstperson_righthand" onclick="loadDispFirstRight()"><i class="material-icons">person</i><div class="tooltip">Firstperson Right</div></label>
                <input class="hidden" type="radio" name="display" id="firstperson_lefthand">
                <label class="tool" for="firstperson_lefthand" onclick="loadDispFirstLeft()"><i class="material-icons">person</i><div class="tooltip">Firstperson Left</div></label>

                <input class="hidden" type="radio" name="display" id="head">
                <label class="tool" for="head" onclick="loadDispHead()"><i class="material-icons">sentiment_satisfied</i><div class="tooltip">Head</div></label>

                <input class="hidden" type="radio" name="display" id="ground">
                <label class="tool" for="ground" onclick="loadDispGround()"><i class="icon-ground"></i><div class="tooltip">Ground</div></label>

                <input class="hidden" type="radio" name="display" id="fixed">
                <label class="tool" for="fixed" onclick="loadDispFixed()"><i class="material-icons">filter_frames</i><div class="tooltip">Frame</div></label>

                <input class="hidden" type="radio" name="display" id="gui">
                <label class="tool" for="gui" onclick="loadDispGUI()"><i class="material-icons">border_style</i><div class="tooltip">GUI</div></label>
            </div>
            <p class="reference_model_bar">Reference Model</p>
            <div id="display_ref_bar" class="bar tabs_small reference_model_bar">
            </div>

            <p>Rotation</p><div class="tool head_right" onclick="resetDisplaySettings('rotation')"><i class="material-icons">replay</i></div>
            <div class="bar">
                <input type="range" class="tool disp_range" id="rotation_x" name="" min="-180" max="180" step="1" value="0" oninput="syncDispInput(this, 'rotation', 'x')">
                <input type="number" class="tool disp_text" id="rotation_x" oninput="syncDispInput(this, 'rotation', 'x')" min="-180" max="180" step="0.5" value="0">
            </div>
            <div class="bar">
                <input type="range" class="tool disp_range" id="rotation_y" name="" min="-180" max="180" step="1" value="0" oninput="syncDispInput(this, 'rotation', 'y')">
                <input type="number" class="tool disp_text" id="rotation_y" oninput="syncDispInput(this, 'rotation', 'y')" min="-180" max="180" step="0.5" value="0">
            </div>
            <div class="bar">
                <input type="range" class="tool disp_range" id="rotation_z" name="" min="-180" max="180" step="1" value="0" oninput="syncDispInput(this, 'rotation', 'z')">
                <input type="number" class="tool disp_text" id="rotation_z" oninput="syncDispInput(this, 'rotation', 'z')" min="-180" max="180" step="0.5" value="0">
            </div>

            <p>Translation</p><div class="tool head_right" onclick="resetDisplaySettings('translation')"><i class="material-icons">replay</i></div>
            <div class="bar">
                <input type="range" class="tool disp_range" id="translation_x" name="" min="-32" max="32" step="0.5" value="0" oninput="syncDispInput(this, 'translation', 'x')">
                <input type="number" class="tool disp_text" id="translation_x" oninput="syncDispInput(this, 'translation', 'x')" min="-80" max="80" step="0.5" value="0">
            </div>
            <div class="bar">
                <input type="range" class="tool disp_range" id="translation_y" name="" min="-32" max="32" step="0.5" value="0" oninput="syncDispInput(this, 'translation', 'y')">
                <input type="number" class="tool disp_text" id="translation_y" oninput="syncDispInput(this, 'translation', 'y')" min="-80" max="80" step="0.5" value="0">
            </div>
            <div class="bar">
                <input type="range" class="tool disp_range" id="translation_z" name="" min="-32" max="32" step="0.5" value="0" oninput="syncDispInput(this, 'translation', 'z')">
                <input type="number" class="tool disp_text" id="translation_z" oninput="syncDispInput(this, 'translation', 'z')" min="-80" max="80" step="0.5" value="0">
            </div>
            
            <p>Scale</p><div class="tool head_right" onclick="resetDisplaySettings('scale')"><i class="material-icons">replay</i></div>
            <div class="bar">
                <input type="range" class="tool disp_range scaleRange" id="scale_x" name="" min="-4" max="4" step="0.1" value="0" oninput="syncDispInput(this, 'scaleRange', 'x', event)">
                <input type="number" class="tool disp_text scale" id="scale_x" oninput="syncDispInput(this, 'scale', 'x')" step="0.1" min="0" max="4">
            </div>
            <div class="bar">
                <input type="range" class="tool disp_range scaleRange" id="scale_y" name="" min="-4" max="4" step="0.1" value="0" oninput="syncDispInput(this, 'scaleRange', 'y', event)">
                <input type="number" class="tool disp_text scale" id="scale_y" oninput="syncDispInput(this, 'scale', 'y')" step="0.1" min="0" max="4">
            </div>
            <div class="bar">
                <input type="range" class="tool disp_range scaleRange" id="scale_z" name="" min="-4" max="4" step="0.1" value="0" oninput="syncDispInput(this, 'scaleRange', 'z', event)">
                <input type="number" class="tool disp_text scale" id="scale_z" oninput="syncDispInput(this, 'scale', 'z')" step="0.1" min="0" max="4">
            </div>
            <p>Options</p>
            <div class="bar">
                <div class="tool" onclick="copyDisplaySlot()"><i class="material-icons">content_copy</i><div class="tooltip">Copy</div></div>
                <div class="tool" onclick="pasteDisplaySlot()"><i class="material-icons">content_paste</i><div class="tooltip">Paste</div></div>
                <div class="tool" onclick="displayPresetContext(event)"><i class="fa fa_big fa-list"></i><div class="tooltip">Presets</div></div>
                <div class="tool" onclick="showDialog('create_preset')"><i class="material-icons">add</i><div class="tooltip">New Preset</div></div>
            </div>
        </div>
        <div id="textures" class="ui m_edit">
            <h3>Textures</h3>
            <div class="bar">
                <div class="tool" onclick="openTexture()"><i class="material-icons">library_add</i><div class="tooltip">Import Texture</div></div>
                <div class="tool" onclick="Painter.addBitmapDialog()"><i class="material-icons">check_box_outline_blank</i><div class="tooltip">Create Blank</div></div>
                <div class="tool local_only" onclick="reloadTextures()"><i class="material-icons">refresh</i><div class="tooltip">Reload Textures</div></div>
                <div class="tool" onclick="TextureAnimator.start()" id="texture_animation_button" style="display: none;"><i class="material-icons">play_arrow</i><div class="tooltip">
                Animations</div></div>
            </div>
            <ul id="texture_list" class="list">
                <li
                    v-for="texture in textures"
                    v-bind:class="{ selected: texture.selected }"
                    v-bind:texid="texture.id"
                    class="texture"
                    v-on:click.stop="texture.select()"
                    v-on:dblclick="texture.openMenu($event)"
                    @contextmenu.prevent.stop="texture.showContextMenu($event)"
                >
                    <div class="texture_icon_wrapper">
                        <img v-bind:texid="texture.id" v-bind:src="texture.source" class="texture_icon" width="48px" alt="missing image" v-if="texture.show_icon" />
                        <i class="material-icons texture_error" title="Image Error" v-if="texture.error">error_outline</i>
                        <i class="texture_movie fa fa_big fa-film" title="Animated Texture" v-if="texture.frameCount > 1"></i>
                    </div>
                    <div class="texture_name">{{ texture.name }}</div>
                    <i class="material-icons texture_mode_toggle" v-bind:title="capitalizeFirstLetter(texture.mode)" v-on:click.stop="texture.convert()">
                        <template v-if="texture.mode === 'link'">link</template>
                        <template v-else>stop</template>
                    </i>
                    <i class="material-icons" title="Particle" v-on:click.stop="toggleP(texture)">
                        <template v-if="texture.particle === true">grain</template>
                        <template v-else>remove</template>
                    </i>
                </li>
            </ul>
        </div>
        <div class="placeholder m_disp"></div><br>
        <button class="large m_disp" type="button" onclick="exitDisplaySettings()">Edit Mode</button>
    </div>
    <div id="right_bar" class="sidebar">
        <div id="options" class="ui selection_only">
            <h3>Rotation</h3>
            <div class="bar">
            <div class="placeholder"></div>Angle
            </div>
            <div class="bar" id="rotation_main_bar" style="position: relative;">
                <div id="cube_rotate_dummy"></div>
                <input type="range" class="tool half rotation_tool" id="cube_rotate" name="cube_rotate" min="-67.5" max="67.5" step="22.5" value="0" onmousedown="Rotation.start()" oninput="Rotation.slide()" onmouseup="Rotation.save()">
                <select class="tool half rotation_tool" id="cube_axis" name="cube_axis" onchange="Rotation.selectTool()">
                    <option value="x" id="x">X Axis</option>
                    <option value="y" id="y" selected>Y Axis</option>
                    <option value="z" id="z">Z Axis</option>
                </select>
                <div class="tool" id="cube_rescale_tool"><input type="checkbox" id="cube_rescale" class="rotation_tool" onclick="Rotation.set()"><div class="tooltip">Rescale</div></div>
                <div class="tool right_tool" id="rotation_function_button" onclick="Rotation.fn()"><i class="material-icons">clear</i><div class="tooltip">Remove Rotation</div></div>
            </div>
            <div class="bar">
                <div class="placeholder"></div><div id="rotation_origin_label">Origin</div>
            </div>
            <div class="bar">
            <div class="tool wide nslide_tool"><div class="nslide" n-action="origin_x"></div><div class="tooltip">Origin X</div></div>
            <div class="tool wide nslide_tool"><div class="nslide" n-action="origin_y"></div><div class="tooltip">Origin Y</div></div>
            <div class="tool wide nslide_tool"><div class="nslide" n-action="origin_z"></div><div class="tooltip">Origin Z</div></div>
                <div class="tool right_tool" id="origin2geometry" onclick="origin2geometry()"><i class="material-icons">center_focus_strong</i><div class="tooltip clip_right">Origin To Geometry</div></div>
            </div>
        </div>
        <div id="outliner" class="ui">
            <h3>Outliner</h3>
            <div class="bar m_edit">
                <div class="tool" onclick="addCube()"><i class="material-icons">add_box</i><div class="tooltip">Add Cube</div></div>
                <div class="tool" onclick="addGroup()"><i class="material-icons">create_new_folder</i><div class="tooltip">Add Group</div></div>
                <div class="tool" id="outliner_option_toggle" onclick="toggleOutlinerOptions()"><i class="material-icons">view_stream</i><div class="tooltip">More Options</div></div>
                <div id="outliner_stats">0/0</div>
            </div>
            <ul id="cubes_list" class="list">
                <vue-tree :option="option"></vue-tree>
            </ul>
        </div>
    </div>
    <div id="preview">
        <canvas id="canvas">
            An error occurred
        </canvas>
    </div>
    <div id="status_bar">
        <div id="status_saved">
            <i class="material-icons" v-if="Prop.project_saved" title="Model is saved">check</i>
            <i class="material-icons" v-else title="There are unsaved changes">close</i>
        </div>
        <div id="status_name">
            {{ Prop.file_name }}
        </div>
        <div id="status_message" class="hidden"></div>
        <div class="f_right">
            {{ Prop.zoom }}%
        </div>
        <div class="f_right">
            {{ Prop.fps }} FPS
        </div>
    </div>
    <div id="donation_hint" class="hidden m_disp">This could be your skin. <a onclick="localStorage.setItem('donated', 'true')" class="open-in-browser" href="http://blockbench.net/donate.html">Learn more</a></div>
    <div id="scene_controls" class="bar hidden">
        <div class="tool" id="scene_controls_toggle" onclick="toggleScenePanel()"><i class="material-icons">first_page</i><div class="tooltip">Settings</div></div>
        <div id="scene_controls_panel">
            <label for="scene_size">Size:</label>
            <input type="number" min="1" step="10" class="tool dark_bordered" oninput="updateScenePanelControls()" id="scene_size">

            <label for="scene_x">Left:</label>
            <input type="number" step="10" class="tool dark_bordered" oninput="updateScenePanelControls()" id="scene_x">

            <label for="scene_y">Top:</label>
            <input type="number" step="10" class="tool dark_bordered" oninput="updateScenePanelControls()" id="scene_y">

            <label for="scene_fixed" class="scene_lock">Lock:</label>
            <input type="checkbox" onclick="updateScenePanelControls()" id="scene_fixed" class="scene_lock">

            <div class="tool" onclick="loadBackgroundImage(event)"><i class="material-icons">folder</i><div class="tooltip">Change Image</div></div>
            <div class="tool" onclick="clearBackgroundImage()"><i class="material-icons">delete</i><div class="tooltip">Clear</div></div>
        </div>
        <img width="30" height="30" src="" onload="updateBackgroundRatio()" />
    </div>
    <div class="mobile_only" id="mobile_tab_bar">
        <div class="mobile_mode_tab open" id="mobile_tab_preview" onclick="setMobileTab('preview')">Preview</div>
        <div class="mobile_mode_tab" id="mobile_tab_textures" onclick="setMobileTab('textures')">Textures</div>
        <div class="mobile_mode_tab" id="mobile_tab_elements" onclick="setMobileTab('elements')">Elements</div>
        <div class="mobile_mode_tab" id="mobile_tab_menu" onclick="setMobileTab('menu')">Menu</div>
    </div>
    <script>
        canvas1 = document.getElementById('canvas')
        initCanvas()
        colorSettingsSetup()
        scenesSetup()
        animate()
        initializeApp()
    </script>
</body>
</html>