<?php
if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}

$plugin = plugin::byId('viessmannIot');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());

?>

<div class="row row-overflow">
	<!-- Page d'accueil du plugin -->
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<div class="row">
			<div class="col-sm-10">
				<legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
				<!-- Boutons de gestion du plugin -->
				<div class="eqLogicThumbnailContainer">
					<div class="cursor eqLogicAction logoPrimary" data-action="add">
						<i class="fas fa-plus-circle"></i>
						<br>
						<span>{{Ajouter}}</span>
					</div>
					<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
						<i class="fas fa-wrench"></i>
						<br>
						<span>{{Configuration}}</span>
					</div>
				</div>
			</div>
			<?php
			// à conserver
			// sera afficher uniquement si l'utilisateur est en version 4.4 ou supérieur
			$jeedomVersion = jeedom::version() ?? '0';
			$displayInfoValue = version_compare($jeedomVersion, '4.4.0', '>=');
			if ($displayInfoValue) {
				?>
				<div class="col-sm-2">
					<legend><i class=" fas fa-comments"></i> {{Community}}</legend>
					<div class="eqLogicThumbnailContainer">
						<div class="cursor eqLogicAction logoSecondary" data-action="createCommunityPost">
							<i class="fas fa-ambulance"></i>
							<br>
							<span style="color:var(--txt-color)">{{Créer un post Community}}</span>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<legend><i class="fas fa-table"></i> {{Mes Equipements}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<br><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun équipement trouvé, cliquer sur "Ajouter" pour commencer}}</div>';
		} else {
			// Champ de recherche
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			// Liste des équipements du plugin
			echo '<div class="eqLogicThumbnailContainer">';
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $eqLogic->getImage() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '<span class="hiddenAsCard displayTableRight hidden">';
				echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Equipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Equipement non visible}}"></i>';
				echo '</span>';
				echo '</div>';
			}
			echo '</div>';
		}
		?>
	</div> <!-- /.eqLogicThumbnailDisplay -->
<div class="col-xs-12 eqLogic" style="display: none;">
  <div class="input-group pull-right" style="display:inline-flex">
    <span class="input-group-btn">
      <a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i>
        {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i
          class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction"
        data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a
        class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i
          class="fas fa-minus-circle"></i> {{Supprimer}}</a>
    </span>
  </div>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab"
        data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
    <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i
          class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
    <li role="presentation"><a href="#widgettab" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i>
        {{Widget}}</a></li>
    <li role="presentation"><a href="#donneestab" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i>
        {{Données supplémentaires}}</a></li>
    <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i
          class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  </ul>
  <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
    <div role="tabpanel" class="tab-pane active" id="eqlogictab">
      <br />
      <form class="form-horizontal">
          <legend><i class="fas fa-wrench"></i> {{Général}}</legend>
          <div class="form-group">
            <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
              <input type="text" class="eqLogicAttr form-control" data-l1key="name"
                placeholder="{{Nom de l'équipement}}" />
            </div>
          </div>
          <div class="form-group">
          <label class="col-sm-4 control-label">{{Objet parent}}</label>
								<div class="col-sm-6">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
										$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Catégorie}}</label>
								<div class="col-sm-6">
									<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" >' . $value['name'];
										echo '</label>';
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Options}}</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr"
											data-l1key="isEnable" checked>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr"
											data-l1key="isVisible" checked>{{Visible}}</label>
								</div>
							</div>
          <legend><i class="fas fa-cogs"></i> {{Paramètres}}</legend>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Id Client}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="clientId"
                placeholder="Id Client" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Code Challenge}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="codeChallenge"
                placeholder="Code Challenge" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Nom d'utilisateur}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="userName"
                placeholder="Nom d'utilisateur Viessmann" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Mot de passe}}</label>
            <div class="col-sm-3">
              <input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="password"
                placeholder="Mot de passe Viessmann" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Numéro chaudière}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="numChaudiere"
                placeholder="Numéro de la chaudière (débute à 0)" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Id de l'installation}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="installationId"
                placeholder="Id installation" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Serial}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="serial"
                placeholder="Serial" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Id du device}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="deviceId"
                placeholder="Id du device" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Id du circuit}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="circuitId"
                placeholder="Id du circuit" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Log Features}}
              <sup><i class="fas fa-question-circle tooltips"
                  title="{{Le json est à récupérer dans le répertoire data du plugin}}"></i></sup>
            </label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="logFeatures"
                placeholder="Mettre Oui" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{(Re)Créer Commandes}}
              <sup><i class="fas fa-question-circle tooltips"
                  title="{{En cas d'ajout d'informations par Viessmann par exemple}}"></i></sup>
            </label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="createCommands"
                placeholder="Mettre Oui" />
            </div>
          </div>
        </fieldset>
      </form>
    </div>

    <div role="tabpanel" class="tab-pane" id="widgettab">
      <form class="form-horizontal">
        <fieldset>
          <br /><br />

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Nom du circuit}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="circuitName"
                placeholder="Nom du circuit" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Unité Gaz}}</label>
            <div class="col-sm-3">
              <select required class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="uniteGaz">
                <option value="" disabled selected>{{Sélectionnez l'unité}}</option>
                <option value="m3">m3( défaut )</option>
                <option value="kWh">kWh</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Facteur de conversion}}</label>
            <div class="col-sm-3">
              <input type="text" class="eqLogicAttr form-control" data-l1key="configuration"
                data-l2key="facteurConversionGaz" placeholder="m3 -> kWh ou kWh -> m3" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Utiliser le widget du plugin}}</label>
            <div class="col-sm-3 form-check-input">
              <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isWidgetPlugin"
                checked />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Afficher la tuile eau chaude}}</label>
            <div class="col-sm-3 form-check-input">
              <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="displayWater"
                checked />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Afficher la tuile gaz}}</label>
            <div class="col-sm-3 form-check-input">
              <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="displayGas"
                checked />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Afficher la tuile électricité}}</label>
            <div class="col-sm-3 form-check-input">
              <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="displayPower"
                checked />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">{{Afficher la tuile PAC}}</label>
            <div class="col-sm-3 form-check-input">
              <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="displayPAC"
                checked />
            </div>
          </div>

        </fieldset>
      </form>
    </div>

    <div role="tabpanel" class="tab-pane" id="donneestab">
      <form class="form-horizontal">
        <fieldset>
          <br /><br />
          <div class="form-group">
            <label class="col-sm-2 control-label">{{Température intérieure}}</label>
            <div class="col-sm-4">
              <div class="input-group">
                <input type="text" class="eqLogicAttr form-control tooltips roundedLeft" data-l1key="configuration"
                  data-l2key="temperature_interieure" data-concat="1" />
                <span class="input-group-btn">
                  <a class="btn btn-default listCmdInfo roundedRight"><i class="fas fa-list-alt"></i></a>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">{{Température extérieure}}</label>
            <div class="col-sm-4">
              <div class="input-group">
                <input type="text" class="eqLogicAttr form-control tooltips roundedLeft" data-l1key="configuration"
                  data-l2key="temperature_exterieure" data-concat="1" />
                <span class="input-group-btn">
                  <a class="btn btn-default listCmdInfo roundedRight"><i class="fas fa-list-alt"></i></a>
                </span>
              </div>
            </div>
          </div>
        </fieldset>
      </form>
    </div>

    <div role="tabpanel" class="tab-pane" id="commandtab">
      <a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i
          class="fa fa-plus-circle"></i> {{Commandes}}</a><br /><br />
      <table id="table_cmd" class="table table-bordered table-condensed">
        <thead>
          <tr>
            <th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
            <th style="min-width:200px;width:350px;">{{Nom}}</th>
            <th>{{Type}}</th>
            <th style="min-width:260px;">{{Options}}</th>
            <th>{{Etat}}</th>
            <th style="min-width:80px;width:200px;">{{Actions}}</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, nom_du_plugin) -->
<?php include_file('desktop', 'viessmannIot', 'js', 'viessmannIot'); ?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js');
