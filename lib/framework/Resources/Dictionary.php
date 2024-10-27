<?php
namespace Lib\Framework\Resources;

const DICTIONARY = array(
   'agenda' => array(
      'pk' => 'idagenda',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('AGENDA_TH_01', 'AGENDA_TH_02', 'AGENDA_TH_03', 'AGENDA_TH_04', 'AGENDA_TH_05', 'AGENDA_TH_06', 'AGENDA_TH_07'),
      'indexes' => array('idagenda', 'age_data', 'age_hora', 'age_status', 'pessoa_idpessoa', 'configuracao_idconfiguracao', 'type')
   ),
   'caixa' => array(
      'pk' => 'idcaixa',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('CAIXA_TH_01', 'CAIXA_TH_02', 'CAIXA_TH_03', 'CAIXA_TH_04', 'CAIXA_TH_05', 'CAIXA_TH_06', 'CAIXA_TH_07', 'CAIXA_TH_08', 'CAIXA_TH_09'),
      'indexes' => array(
         'idcaixa', 'recebimento_idpagamento', 'pagamento_idpagamento', 'cx_data_emissao',
         'cx_data_vencimento', 'cx_data_baixa', 'cx_valor_documento', 'cx_valor_desconto', 'cx_valor_pago'
      )
   ),
   'configuracao' => array(
      'pk' => 'idconfiguracao',
      'fk' => '',
      'lookupfield' => 'con_descricao',
      'header' => array('CONFIG_TH_01', 'CONFIG_TH_02', 'CONFIG_TH_03', 'CONFIG_TH_04', 'CONFIG_TH_05', 'CONFIG_TH_06'),
      'indexes' => array('idconfiguracao', 'con_descricao', 'con_valor_hora', 'con_tipo', 'con_perc_honorario', 'con_pais')
   ),
   'contract' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('CONTRACT_TH_01', 'CONTRACT_TH_02', 'CONTRACT_TH_03', 'CONTRACT_TH_04', 'CONTRACT_TH_05', 'CONTRACT_TH_06'),
      'indexes' => array('id', 'professional', 'client', 'status', 'startDate', 'finishDate')
   ),
   'invoice' => array(
      'pk' => 'InvoiceNumber',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('INVOICE_TH_01', 'INVOICE_TH_02', 'INVOICE_TH_03', 'INVOICE_TH_04', 'INVOICE_TH_05', 'INVOICE_TH_06'),
      'indexes' => array('InvoiceNumber', 'ClientName', 'CPF', 'SessionDate', 'ProfessionalName', 'ProfessionalCRP')
   ),
   'log' => array(
      'pk' => 'idlog',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('LOG_TH_01', 'LOG_TH_02', 'LOG_TH_03'),
      'indexes' => array('idlog', 'log_data', 'log_atividade')
   ),
   'mensagem' => array(
      'pk' => 'idmensagem',
      'fk' => '',
      'lookupfield' => 'assunto',
      'header' => array('MSG_TH_01', 'MSG_TH_02', 'MSG_TH_03'),
      'indexes' => array('idmensagem', 'assunto', 'regra')
   ),
   'pacoteconsulta' => array(
      'pk' => 'idpacoteconsulta',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('PKG_TH_01', 'PKG_TH_02', 'PKG_TH_03', 'PKG_TH_04', 'PKG_TH_05', 'PKG_TH_06', 'PKG_TH_07'),
      'indexes' => array(
         'idpacoteconsulta', 'professional', 'client', 'pc_data_selecao', 'pc_valor_total_pacote',
         'pc_qtd_total_pacote', 'ppp_preco'
      )
   ),
   'pagamento' => array(
      'pk' => 'idpagamento',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('PMT_TH_01', 'PMT_TH_02', 'PMT_TH_03', 'PMT_TH_04', 'PMT_TH_05', 'PMT_TH_06', 'PMT_TH_07'),
      'indexes' => array('idpagamento', 'pessoa_agenda_idpessoa_agenda', 'pag_documento', 'pag_data_emissao', 'pag_data_pagamento', 'pag_status_pagto', 'pag_valor')
   ),
   'pessoa' => array(
      'pk' => 'idpessoa',
      'fk' => '',
      'lookupfield' => 'pes_nome',
      'header' => array('PPL_TH_01', 'PPL_TH_02', 'PPL_TH_03', 'PPL_TH_04', 'PPL_TH_09', 'PPL_TH_05', 'PPL_TH_06', 'PPL_TH_07', 'PPL_TH_08', ),
      'indexes' => array('idpessoa', 'pes_login', 'pes_nome', 'pes_datanasc', 'createDate', 'pes_pais', 'pes_estado', 'pes_cidade', 'pes_ativo', 'pes_ind_cons_bonus')
   ),
   'pessoa_agenda' => array(
      'pk' => 'idpessoa_agenda',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('SESSION_TH_01', 'SESSION_TH_02', 'SESSION_TH_03', 'SESSION_TH_04', 'SESSION_TH_05', 'SESSION_TH_06', 'SESSION_TH_07'),
      'indexes' => array('idpessoa_agenda', 'pacoteconsulta_idpacoteconsulta', 'professional', 'client', 'pa_data', 'pa_hora', 'pa_status')
   ),
   'precoporpacote' => array(
      'pk' => 'idprecoporpacote',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('PROMO_TH_01', 'PROMO_TH_02', 'PROMO_TH_03', 'PROMO_TH_04', 'PROMO_TH_05'),
      'indexes' => array('idprecoporpacote', 'ppp_quantidade', 'ppp_preco', 'ppp_pais', 'ppp_type')
   ),
   'recebimento' => array(
      'pk' => 'idpagamento',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('REC_TH_01', 'REC_TH_09', 'REC_TH_02', 'REC_TH_03', 'REC_TH_04', 'REC_TH_05', 'REC_TH_06', 'REC_TH_07', 'REC_TH_08'),
      'indexes' => array(
         'idpagamento', 'pacoteconsulta_idpacoteconsulta', 'pessoa_agenda_idpessoa_agenda', 'rec_documento',
         'rec_token_gateway', 'rec_data_emissao', 'rec_data_pagamento', 'rec_status_pagto',
         'rec_valor'
      )
   ),
   'tesouraria' => array(
      'pk' => 'rec_token_gateway',
      'fk' => '',
      'lookupfield' => '',
      'header' => array(
         'TREAS_TH_01', 'TREAS_TH_02', 'TREAS_TH_03', 'TREAS_TH_04', 'TREAS_TH_05', 'TREAS_TH_06',
         'TREAS_TH_07', 'TREAS_TH_08', 'TREAS_TH_09', 'TREAS_TH_10'
      ),
      'indexes' => array(
         'placeHolder', 'paypalCurrency', 'paypalGrossPaymentValue', 'paypalTransactionPaymentValue',
         'professionalNetPaymentValue', 'platformNetPaymentValue', 'professionalNetBRLPaymentValue',
         'platformNetBRLPaymentValue', 'paypalServiceCADPaymentValue', 'platformNetCADPaymentValue'
      )
   ),
   'vault' => array(
      'pk' => 'idpessoa',
      'fk' => '',
      'lookupfield' => '',
      'header' => array(
         'VAULT_TH_01', 'VAULT_TH_03', 'VAULT_TH_04', 'VAULT_TH_05', 'VAULT_TH_06', 'VAULT_TH_07',
      ),
      'indexes' => array(
         'idvault', 'pes_nome', 'pes_login', 'acct1_Signature', 'ps_credentials_token', 'createDate'
      )
   ),
   'pricetable' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('PRICETBL_TH_01', 'PRICETBL_TH_02', 'PRICETBL_TH_03', 'PRICETBL_TH_04', 'PRICETBL_TH_05', 'PRICETBL_TH_06', 'PRICETBL_TH_07'),
      'indexes' => array('id', 'pes_nome', 'startdate', 'enddate', 'status', 'type', 'price')
   ),
   'endereco' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('ADDRESS_TH_01', 'ADDRESS_TH_02', 'ADDRESS_TH_03', 'ADDRESS_TH_04', 'ADDRESS_TH_05', 'ADDRESS_TH_06', 'ADDRESS_TH_07', 'ADDRESS_TH_08', 'ADDRESS_TH_09', 'ADDRESS_TH_10'),
      'indexes' => array('id', 'pes_nome', 'endereco', 'numero', 'complemento', 'bairro', 'cep', 'cidade', 'estado', 'pais')
   ),
   // Relatorios
   'relatorio_perfil_cliente' => array(
      'pk' => 'idpessoa',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('REPORT_CLIENT_PROFILE_TH_01', 'REPORT_CLIENT_PROFILE_TH_02', 'REPORT_CLIENT_PROFILE_TH_03', 'REPORT_CLIENT_PROFILE_TH_04', 'REPORT_CLIENT_PROFILE_TH_05', 'REPORT_CLIENT_PROFILE_TH_06', 'REPORT_CLIENT_PROFILE_TH_07', 'REPORT_CLIENT_PROFILE_TH_08'),
      'indexes' => array('idpessoa', 'pes_nome', 'pes_login', 'pes_datanasc', 'pes_cidade', 'pes_estado', 'pes_profissao', 'pes_estadocivil'),
      'total' => array()
   ),
   'relatorio_honorarios_psicologo' => array(
      'pk' => 'pes_nome',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('REPORT_PAYMENT_PROFESSIONAL_TH_01', 'REPORT_PAYMENT_PROFESSIONAL_TH_02', 'REPORT_PAYMENT_PROFESSIONAL_TH_03', 'REPORT_PAYMENT_PROFESSIONAL_TH_04', 'REPORT_PAYMENT_PROFESSIONAL_TH_05'),
      'indexes' => array('pes_nome', 'pa_data', 'pa_hora', 'pa_preco_consulta', 'price'),
      'total' => array('pa_preco_consulta', 'price')
   ),
   'relatorio_pagamentos_cliente' => array(
      'pk' => 'pes_nome',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('REPORT_PAYMENT_CUSTOMER_TH_01', 'REPORT_PAYMENT_CUSTOMER_TH_02', 'REPORT_PAYMENT_CUSTOMER_TH_03', 'REPORT_PAYMENT_CUSTOMER_TH_04'),
      'indexes' => array('pes_nome', 'pa_data', 'pa_hora', 'pa_preco_consulta'),
      'total' => array('pa_preco_consulta')
   ),
   'relatorio_horas_marcadas_x_horas_vendidas' => array(
      'pk' => 'age_data',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('REPORT_APPOINTED_HOURS_SOLD_HOURS_TH_01', 'REPORT_APPOINTED_HOURS_SOLD_HOURS_TH_02', 'REPORT_APPOINTED_HOURS_SOLD_HOURS_TH_03'),
      'indexes' => array('age_data', 'free_hours', 'sold_hours'),
      'total' => array('sold_hours')
   ),
   'relatorio_consultas_marcadas_por_periodo_psicologo_e_paciente' => array(
      'pk' => 'profissional',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('REPORT_APPOINTMENTS_SCHEDULED_BY_PROFESSIONAL_CUSTOMER_TH_01', 'REPORT_APPOINTMENTS_SCHEDULED_BY_PROFESSIONAL_CUSTOMER_TH_02', 'REPORT_APPOINTMENTS_SCHEDULED_BY_PROFESSIONAL_CUSTOMER_TH_03', 'REPORT_APPOINTMENTS_SCHEDULED_BY_PROFESSIONAL_CUSTOMER_TH_04', 'REPORT_APPOINTMENTS_SCHEDULED_BY_PROFESSIONAL_CUSTOMER_TH_05'),
      'indexes' => array('profissional', 'cliente', 'pa_data', 'pa_hora', 'status'),
      'total' => array()
   ),
   'relatorio_consultas_marcadas_por_periodo' => array(
      'pk' => 'PSICOLOGA',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_01', 'REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_02', 'REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_03', 'REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_04', 'REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_05', 'REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_06', 'REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_07', 'REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_08', 'REPORT_APPOINTMENTS_SCHEDULED_BY_DATE_TH_09'),
      'indexes' => array('PSICOLOGA', 'CONFIGURACAO', 'CLIENTE', 'DATACOMPRA', 'N_PACOTE', 'DATACONSULTA', 'VALORPAGO', 'VALORPSIC', 'VALORCONEXPERIENCE'),
      'total' => array()
   ),
   
   'empresa' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('EMPRESA_TH_01', 'EMPRESA_TH_02', 'EMPRESA_TH_10'),
      'indexes' => array('cnpj', 'nome', 'status'),
      'total' => array()
   ),

   'colaborador' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('COLABORADOR_TH_01', 'COLABORADOR_TH_02', 'COLABORADOR_TH_03','COLABORADOR_TH_04','COLABORADOR_TH_05', 'COLABORADOR_TH_06', 'COLABORADOR_TH_07'),
      'indexes' => array('id', 'pes_nome', 'pes_login', 'pes_datanasc', 'createDate', 'statusDescription', 'tipoDescription'),
      'total' => array()
   ),

   'users' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('id', 'email', 'status'),
      'indexes' => array('id', 'email', 'status'),
      'total' => array()
   ),

   'acessos' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('ACCESS_TH_01', 'ACCESS_TH_02', 'ACCESS_TH_03', 'ACCESS_TH_04'),
      'indexes' => array('id', 'classe', 'metodo', 'role'),
      'total' => array()
   ),

   'permissions' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('PERMISSIONS_TH_01', 'PERMISSIONS_TH_02', 'PERMISSIONS_TH_03'),
      'indexes' => array('id', 'classe', 'metodo'),
      'total' => array()
   ),

   'roles' => array(
      'pk' => 'id',
      'fk' => '',
      'lookupfield' => '',
      'header' => array('ROLES_TH_01', 'ROLES_TH_02'),
      'indexes' => array('id', 'role'),
      'total' => array()
   ),
);