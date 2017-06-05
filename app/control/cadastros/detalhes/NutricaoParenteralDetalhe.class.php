<?php

class NutricaoParenteralDetalhe extends TStandardList{
    protected $form;

    protected $datagrid; 
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;

    function __construct(){
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_nutricao_parenteral');
        $this->form->setFormTitle('Nutrição Parenteral');
        
        parent::setDatabase('dbsic');
        parent::setActiveRecord('NutricaoParenteralRecord');
        
        $id                                 = new THidden('id');
        $paciente_id                        = new THidden('paciente_id'); 
        $paciente_id->setValue(filter_input(INPUT_GET, 'fk'));

        TTransaction::open('dbsic');
        $tempVisita = new PacienteRecord( filter_input( INPUT_GET, 'fk' ) );
        if( $tempVisita ){
            $paciente_nome = new TLabel( $tempVisita->nome );
            $paciente_nome->setEditable(FALSE);
        }
        TTransaction::close(); 

        $inicio                             = new TDate('datainicio');
        $fim                                = new TDate('datafim');
        //$tipoparenteral                     = new TCombo('tipoparenteral');

        $tipoparenteral = new TCombo('tipoparenteral');
        $items = array();
        $items['CICLICA'] = 'Ciclica';
        $items['CONTINUA'] = 'Contínua';
        $items['OUTRAS'] = 'Outras';

        $tipoparenteral->addItems($items);
        $tipoparenteral->setValue('CICLICA');
        $acaoRadio = new TAction(array($this, 'onChangeRadio'));
        $acaoRadio->setParameter('form_nutricao_parenteral', $this->form->getName());
        $tipoparenteral->setChangeAction($acaoRadio);

        $tipoparenteraloutros               = new TEntry('tipoparenteraloutros');

        $tipoparenteraloutros->setEditable(FALSE);
        $totalcalorias                      = new TEntry('totalcalorias');
        $percentualdiario                   = new TEntry('percentualdiario');
        $volumenpt                          = new TEntry('volumenpt');
        $tempoinfusao                       = new TEntry('tempoinfusao');
        $frequencia                         = new TEntry('frequencia');
        $acessovenosolpqual                 = new TEntry('acessovenosolpqual');
        //$numerodeacessovenoso               = new TEntry('numerodeacessovenoso');

        $numerodeacessovenoso      = new TSpinner('numerodeacessovenoso');
        $numerodeacessovenoso->setRange(1,100,1);
        $numerodeacessovenoso->setValue(1);
        $apresentouinfeccaoacessovenoso     = new TRadioGroup('apresentouinfeccaoacessovenoso');
        $vezesinfeccaoacessovenoso          = new TEntry('vezesinfeccaoacessovenoso');


        $totalcalorias->setMask('99999999999');
        $percentualdiario->setMask('999');
        //$percentualdiario->setMask('99999999999');
        //$percentualdiario->setNumericMask(0, '.', true);

        $tipoparenteraloutros->setProperty( "title", "Informe os tipo de nutrição parenteral aplicada" );
        $frequencia->setProperty( "title", "Informe a frequência da nutrição parenteral por dia" );

        //$acessovenosolp                     = new TRadioGroup('acessovenosolp');

        $acessovenosolp = new TRadioGroup('acessovenosolp');
        $acessovenosolp->addItems(array('SIM'=>'SIM', 'NAO'=>'NÃO'));
        $acessovenosolp->setLayout('horizontal');
        $acaoRadio = new TAction(array($this, 'onChangeRadio2'));
        $acaoRadio->setParameter('form_nutricao_parenteral', $this->form->getName());
        $acessovenosolp->setChangeAction($acaoRadio);
        $acessovenosolp->setValue('SIM');

        //$acessovenosolp->setValue('SIM');
        $apresentouinfeccaoacessovenoso->addItems(array('SIM'=>'SIM', 'NAO'=>'NÃO'));

        $apresentouinfeccaoacessovenoso->setLayout('horizontal');
        //$apresentouinfeccaoacessovenoso->setValue('nao');
        $inicio->setSize('20%');
        $fim->setSize('20%');

        $inicio->setMask('dd/mm/yyyy');
        $fim->setMask('dd/mm/yyyy');
        $inicio->setDatabaseMask('yyyy-mm-dd');
        $fim->setDatabaseMask('yyyy-mm-dd');

        $inicio->addValidation( "Início", new TRequiredValidator );
        $tipoparenteral->addValidation( "Tipo Parenteral", new TRequiredValidator );
        $volumenpt->addValidation( "Tipo da NTP", new TRequiredValidator );
        $acessovenosolp->addValidation( "Acesso Venoso", new TRequiredValidator );
        $apresentouinfeccaoacessovenoso->addValidation( "Apresentou Infecção no Acesso Venoso", new TRequiredValidator );
        


        $this->form->addFields( [new TLabel('Paciente'), $paciente_nome] );
        $this->form->addFields( [new TLabel('Inicio<font color=red><b>*</b></font>')], [$inicio] );
        $this->form->addFields( [new TLabel('Fim')], [$fim] );
        $this->form->addFields( [new TLabel('Tipo da NTP<font color=red><b>*</b></font>')], [$tipoparenteral] );
        $this->form->addFields( [new TLabel('Outros Tipos NTP')], [$tipoparenteraloutros] );
        $this->form->addFields( [new TLabel('Total de Calorias Aplicadas')], [$totalcalorias] );
        $this->form->addFields( [new TLabel('Percentual Diário Necessário')], [$percentualdiario, '%'] );
        $this->form->addFields( [new TLabel('Volume da NPT<font color=red><b>*</b></font>')], [$volumenpt ] );
        $this->form->addFields( [new TLabel('Tempo da Infusão')], [$tempoinfusao] );
        $this->form->addFields( [new TLabel('Frequência da NPT / Dia')], [$frequencia] );
        $this->form->addFields( [new TLabel('Acesso Venoso<font color=red><b>*</b></font>')], [$acessovenosolp] );
        $this->form->addFields( [new TLabel('Qualidade do Acesso Venoso')], [$acessovenosolpqual] );
        $this->form->addFields( [new TLabel('Quantidade de Acessos Venosos de longa permanência')], [$numerodeacessovenoso] );
        $this->form->addFields( [new TLabel('Apresentou Infecção no Acesso Venoso<font color=red><b>*</b></font>')], [$apresentouinfeccaoacessovenoso] );
        $this->form->addFields( [new TLabel('Quantidade de Infecções no Acesso Venoso')], [$vezesinfeccaoacessovenoso] );
        $this->form->addFields( [ $id, $paciente_id ] );

        $action = new TAction(array($this, 'onSave'));
        $action->setParameter('id', '' . filter_input(INPUT_GET, 'id') . '');
        $action->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');

        $this->form->addAction('Salvar', $action, 'fa:floppy-o');
        $this->form->addAction('Voltar para Pacientes',new TAction(array('PacienteList','onReload')),'fa:table blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        $column_name = new TDataGridColumn('paciente_nome', 'Paciente', 'left');
        $column_inicio = new TDataGridColumn('datainicio', 'Início', 'left');
        $column_fim = new TDataGridColumn('datafim', 'Fim', 'left');
        $column_tipoparenteral = new TDataGridColumn('tipoparenteral', 'Tipo Parenteral', 'center');
        $column_tipoparenteraloutros = new TDataGridColumn('tipoparenteraloutros', 'Tipo Parenteral Outros', 'center');
        $column_totalcalorias = new TDataGridColumn('totalcalorias', 'Total Calorias', 'center');
        $column_percentualdiario = new TDataGridColumn('percentualdiario', 'Percentual Diário', 'center');
        $column_volumenpt = new TDataGridColumn('volumenpt', 'Volume NPT', 'left');
        $column_tempoinfusao = new TDataGridColumn('tempoinfusao', 'Tempo Infusão', 'left');
        $column_frequencia = new TDataGridColumn('frequencia', 'frequencia', 'left');
        $column_acessovenosolp = new TDataGridColumn('acessovenosolp', 'acessovenosolp', 'left');
        $column_acessovenosolpqual = new TDataGridColumn('acessovenosolpqual', 'acessovenosolpqual', 'left');
        $column_numerodeacessovenoso = new TDataGridColumn('numerodeacessovenoso', 'numerodeacessovenoso', 'left');
        $column_apresentouinfeccaoacessovenoso = new TDataGridColumn('apresentouinfeccaoacessovenoso', 'apresentouinfeccaoacessovenoso', 'left');
        $column_vezesinfeccaoacessovenoso = new TDataGridColumn('vezesinfeccaoacessovenoso', 'vezesinfeccaoacessovenoso', 'left');

        $this->datagrid->addColumn($column_inicio);
        $this->datagrid->addColumn($column_fim);
        $this->datagrid->addColumn($column_tipoparenteral);
        $this->datagrid->addColumn($column_totalcalorias);
        $this->datagrid->addColumn($column_volumenpt);
        $this->datagrid->addColumn($column_tempoinfusao);



        /*
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_tipoparenteraloutros);
        $this->datagrid->addColumn($column_percentualdiario);
        $this->datagrid->addColumn($column_frequencia);
        $this->datagrid->addColumn($column_acessovenosolp);
        $this->datagrid->addColumn($column_acessovenosolpqual);
        $this->datagrid->addColumn($column_numerodeacessovenoso);
        $this->datagrid->addColumn($column_apresentouinfeccaoacessovenoso);
        $this->datagrid->addColumn($column_vezesinfeccaoacessovenoso);
        */
        
        $action_edit = new TDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter('fk', filter_input(INPUT_GET, 'fk'));
        $this->datagrid->addAction( $action_edit );

        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $action_del->setParameter('fk', filter_input(INPUT_GET, 'fk'));
        $this->datagrid->addAction($action_del);
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);

        parent::add($container);
    }
        public static function onChangeRadio2($param)
   {
       switch ($param['acessovenosolp'])
       {
           case 'SIM':
           TEntry::clearField($param['form_nutricao_parenteral'], 'acessovenosolpqual');
           TEntry::clearField($param['form_nutricao_parenteral'], 'numerodeacessovenoso');
           TEntry::clearField($param['form_nutricao_parenteral'], 'apresentouinfeccaoacessovenoso');
           TEntry::clearField($param['form_nutricao_parenteral'], 'vezesinfeccaoacessovenoso');
           TEntry::enableField($param['form_nutricao_parenteral'], 'acessovenosolpqual');
           TEntry::enableField($param['form_nutricao_parenteral'], 'numerodeacessovenoso');
           TEntry::enableField($param['form_nutricao_parenteral'], 'apresentouinfeccaoacessovenoso');
           TEntry::enableField($param['form_nutricao_parenteral'], 'vezesinfeccaoacessovenoso');
           break;
       
           case 'NAO':
           TEntry::clearField($param['form_nutricao_parenteral'], 'acessovenosolpqual');
           TEntry::clearField($param['form_nutricao_parenteral'], 'numerodeacessovenoso');
           TEntry::clearField($param['form_nutricao_parenteral'], 'apresentouinfeccaoacessovenoso');     
           TEntry::clearField($param['form_nutricao_parenteral'], 'vezesinfeccaoacessovenoso');     
           TEntry::disableField($param['form_nutricao_parenteral'], 'acessovenosolpqual');
           TEntry::disableField($param['form_nutricao_parenteral'], 'numerodeacessovenoso');
           TEntry::disableField($param['form_nutricao_parenteral'], 'apresentouinfeccaoacessovenoso');
           TEntry::disableField($param['form_nutricao_parenteral'], 'vezesinfeccaoacessovenoso');
           break;
       }
   }

    public static function onChangeRadio($param){

        if($param['tipoparenteral'] == 'OUTRAS'){
            TEntry::clearField($param['form_nutricao_parenteral'], 'tipoparenteraloutros');
            TEntry::enableField($param['form_nutricao_parenteral'], 'tipoparenteraloutros');
        }else{
            TEntry::clearField($param['form_nutricao_parenteral'], 'tipoparenteraloutros');     
            TEntry::disableField($param['form_nutricao_parenteral'], 'tipoparenteraloutros');
            
            }
    }

    function onEdit( $param ){
        try{
            if( isset( $param[ "key" ] ) ){
                TTransaction::open( "dbsic" );
                $object = new NutricaoParenteralRecord( $param[ "key" ] );
                $this->form->setData( $object );
                TTransaction::close();
            }
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }

    }
    public function onSave(){
        try{

            TTransaction::open('dbsic');
            $cadastro = $this->form->getData('NutricaoParenteralRecord');
            $this->form->validate();
            $cadastro->store();
            TTransaction::close();

            $param=array();
            $param['key'] = $cadastro->id;
            $param['id'] = $cadastro->id;
            $param['fk'] = $cadastro->paciente_id;
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            TApplication::gotoPage('NutricaoParenteralDetalhe','onReload', $param); 

        }catch (Exception $e){
            $object = $this->form->getData($this->activeRecord);
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onReload( $param = NULL ){
        try{

            TTransaction::open( "dbsic" );

            $repository = new TRepository( "NutricaoParenteralRecord" );
            if ( empty( $param[ "order" ] ) )
            {
                $param[ "order" ] = "id";
                $param[ "direction" ] = "asc";
            }
            $limit = 10;
            
            $criteria = new TCriteria();
            $criteria->add(new TFilter('paciente_id', '=', filter_input(INPUT_GET, 'fk')));
            $criteria->setProperties( $param );
            $criteria->setProperty( "limit", $limit );
            
            $objects = $repository->load( $criteria, FALSE );

            $this->datagrid->clear();
            if ( !empty( $objects ) ){

                foreach ( $objects as $object ){

                    $object->datainicio = TDate::date2br($object->datainicio);
                    $object->datafim = TDate::date2br($object->datafim);
                    $this->datagrid->addItem( $object );
                }
            }
            $criteria->resetProperties();

            $count = $repository->count($criteria);
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
            $this->loaded = true;
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );
        }
    }

    
}
