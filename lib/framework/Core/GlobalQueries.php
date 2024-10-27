<?php

namespace Lib\Framework\Core;

const _MY_SESSION_SQL = "select 
               idpessoa_agenda,
               pa_type, 
               pa_status pa_statusCode,
               pa_ind_cons_bonus,
               professional.pes_idmensageiro, 
               professional.pes_endereco,
               professional.pes_bairro,
               professional.pes_cep,
               professional.pes_cidade,
               professional.pes_estado,
               professional.pes_nome as professionalName,
               client.idpessoa clientId,
               client.pes_nome as clientName,
               client.pes_login as clientLogin,
               pa_data,
               pa_hora,
                case pa_status 
                  when 1 then 'Reservado'
                  when 2 then 'Confirmado'
                  when 3 then 'Marcado'
                  when 4 then 'Realizado'
                  when 5 then 'Remarcado cliente'
                  when 6 then 'Remarcado psicólogo'
                  when 7 then 'Remarcado secretaria'
                  when 8 then 'Remarcado admin'
                  when 9 then 'Cancelado'
               end pa_status,
               case when cast(concat(pa_data, ' ', pa_hora) as datetime) >= DATE_ADD(NOW(), INTERVAL 4 HOUR) then true else false end canReschedule,
               case when 
                     NOW() between cast(concat(cast(pa_data as char(10)), ' ', cast(pa_hora as char(8))) as datetime)  - INTERVAL 10 MINUTE and 
                     cast(concat(cast(pa_data as char(10)), ' ', cast(pa_hora as char(8))) as datetime)  + INTERVAL 80 MINUTE
               then 1 else 0 end showMeetingButton               
               from 
               pessoa_agenda 
               inner join pessoa client on (pessoa_agenda.pessoa_idpessoa = client.idpessoa)
               inner join pessoa professional on (pessoa_agenda.psicologo_idpessoa = professional.idpessoa)
               where
               %s = %s
               %s
               and pa_status in (3,4,5,6,7,8)
               order by 
               cast(concat(pa_data, ' ', pa_hora) as datetime) desc";

const _MY_CLIENTS_SQL = "select 
            professional.idpessoa professionalId,
            professional.pes_nome professional, 
            client.idpessoa clientId,
            client.pes_login client,
            max(pa_data) lastsession,
            round(sum(pa_preco_consulta),2) total,
            count(idpessoa_agenda) qty,
            round((round(sum(pa_preco_consulta),2) / count(idpessoa_agenda)), 2) avgTicket
            from
            pessoa_agenda 
            inner join pessoa professional on (pessoa_agenda.psicologo_idpessoa = professional.idpessoa and professional.pes_tipo = 1)
            inner join pessoa client on (pessoa_agenda.pessoa_idpessoa = client.idpessoa and client.pes_tipo = 2)
            where
            pa_status in (3,4,5,6,7,8)
            %s = %s
            group by
            professional.pes_nome, 
            client.pes_login
            order by
            professional.pes_nome, 
            max(pa_data),
            client.pes_login";

const _MY_CONTRACTS_SQL = "select
            contract.id contractId,
            professional.idpessoa professionalId,
            professional.pes_nome professional, 
            client.idpessoa clientId,
            client.pes_login client,
            max(pa_data) lastsession,
            round(sum(pa_preco_consulta),2) total,
            count(idpessoa_agenda) qty,
            round((round(sum(pa_preco_consulta),2) / count(idpessoa_agenda)), 2) avgTicket
            from
            pessoa_agenda 
            inner join pessoa professional on (pessoa_agenda.psicologo_idpessoa = professional.idpessoa and professional.pes_tipo = 1)
            inner join pessoa client on (pessoa_agenda.pessoa_idpessoa = client.idpessoa and client.pes_tipo = 2)
            inner join contract on (contract.clientId = client.idpessoa and contract.professionalId = professional.idpessoa)
            where
            pa_status in (3,4,5,6,7,8)
            %s = %s
            group by
            professional.pes_nome, 
            client.pes_login
            order by
            professional.pes_nome, 
            max(pa_data),
            client.pes_login";

//In use in Automatic Monitor
const _SQL_UPDATE_SLOT_SELECTED_CLEANUP = "update pessoa_agenda set pa_status = 9, pa_data_hora_alteracao = NOW() where "
    . " pacoteconsulta_idpacoteconsulta = %s and pa_status = 1 and "
    . " timediff('%s', pa_data_hora_marcacao) > '00:%s:00' ";
const _SQL_UPDATE_SLOT_CONFIRMED_CLEANUP = "update pessoa_agenda set pa_status = 9, pa_data_hora_alteracao = NOW() where "
    . " pacoteconsulta_idpacoteconsulta = %s and  pa_status = 2 and "
    . " timediff('%s', pa_data_hora_confirmacao) > '00:%s:00' ";
const _SQL_UPDATE_SLOT_NOT_PAYED_CLEANUP = "update pessoa_agenda set pa_status = 9, pa_data_hora_alteracao = NOW() where "
    . " pacoteconsulta_idpacoteconsulta = %s and  pa_status = 10 and "
    . " timediff('%s', pa_data_hora_pagamento) > '%s' ";
const _SQL_UPDATE_SLOT_RESUMED_CLEANUP = "update pessoa_agenda set pa_status = 4, pa_data_hora_alteracao = NOW() where "
    . " pacoteconsulta_idpacoteconsulta = %s and  pa_status in (3,5,6,7,8)  and "
    . " cast(concat(cast(pa_data as char(10)), ' ', cast(pa_hora as char(8))) as datetime) < '%s' ";
const _SQL_UPDATE_AGENDA_PAST_DAYS_CLEANUP = "update agenda set age_status = 3 where "
    . " cast(concat(cast(age_data as char(10)), ' ', cast(age_hora as char(8))) as datetime) < '%s' ";
const _SQL_UPDATE_SESSION_STATUS = "update pessoa_agenda set pa_status = 3, pa_data_hora_alteracao = NOW() where "
    . " pacoteconsulta_idpacoteconsulta = %s and  pa_status in (10) and idpessoa_agenda = %s ";
const _SQL_UPDATE_BONUS_CONSULTING = "update pessoa set pes_ind_cons_bonus = 0 where idpessoa = %s";
const _SQL_UPDATE_PAYMENT = "update recebimento set rec_status_pagto = %s , rec_data_pagamento = NOW() where "
    . "rec_token_gateway = '%s' ";

const _SQL_UPDATE_SLOT_RESUMED_ALL_CLEANUP = "update pessoa_agenda set pa_status = 4, pa_data_hora_alteracao = NOW() where "
    . " pa_status in (3,5,6,7,8)  and "
    . " cast(concat(cast(pa_data as char(10)), ' ', cast(pa_hora as char(8))) as datetime) < '%s' ";

const _SQL_UPDATE_AGENDA_CLEANUP_FUTURE_SESSION = "UPDATE agenda 
SET 
    age_status = 1
WHERE
    idagenda IN (SELECT 
            agenda_idagenda
        FROM
            pessoa_agenda
        WHERE
            pa_data >= CURRENT_DATE()
                AND pa_status = 9)";

const _SQL_EMAIL_PROFESSIONAL_CLIENT_INFO =
'SELECT DISTINCT
      pessoa_agenda.pacoteconsulta_idpacoteconsulta packageId,
      psicologo.idpessoa professionalId,
      psicologo.pes_nome professionalName,
      psicologo.pes_login professionalEmail,
      psicologo.pes_telefone1 professionalWhatsApp,
      psicologo.pes_idmensageiro professionalChimeId,
      cliente.idpessoa clientId,
      cliente.pes_nome clientName,
      cliente.pes_login clientEmail
      FROM
      pessoa_agenda
         INNER JOIN
      pessoa psicologo ON (pessoa_agenda.psicologo_idpessoa = psicologo.idpessoa)
         INNER JOIN
      pessoa cliente ON (pessoa_agenda.pessoa_idpessoa = cliente.idpessoa)
      WHERE
      pessoa_agenda.pa_status IN (%s)
      %s
      ORDER BY pessoa_agenda.pacoteconsulta_idpacoteconsulta';

const _SQL_SESSION_LIST = 'select * from pessoa_agenda where pacoteconsulta_idpacoteconsulta = %s and '
    . 'pa_status in(%s) %s  order by pacoteconsulta_idpacoteconsulta, idpessoa_agenda';

const _SQL_RESCHEDULED_SESSION_LIST = "
      SELECT 
         pessoa_agenda.pacoteconsulta_idpacoteconsulta pacote,
         pessoa_agenda.pa_type,
         pessoa_agenda.idpessoa_agenda id,
         pessoa_agenda.agenda_idagenda agenda,
         psicologo.idpessoa cd_psicologo,
         psicologo.pes_nome nm_psicologo,
         psicologo.pes_login email_psicologo,
         psicologo.pes_idmensageiro,
         cliente.pes_nome nm_cliente,
         cliente.idpessoa cd_cliente,
         cliente.pes_login email_cliente,
         pessoa_agenda.pa_data data,
         pessoa_agenda.pa_hora hora,
         pessoa_agenda.pa_status status,
         pessoa_agenda.pa_alerta_marcacao,
         pessoa_agenda.pa_alerta_confirmacao,
         pessoa_agenda.pa_alerta_alteracao,
         pessoa_agenda.pa_alerta_pagamento,
         pessoa_agenda.pa_preco_consulta,
         pessoa_agenda.pa_id_consulta_original,
         consultaoriginal.pa_data data_original,
         consultaoriginal.pa_hora hora_original
      FROM
         pessoa_agenda
            LEFT JOIN
         pessoa_agenda consultaoriginal ON (pessoa_agenda.pa_id_consulta_original = consultaoriginal.idpessoa_agenda)
            INNER JOIN
         agenda ON (agenda.idagenda = pessoa_agenda.agenda_idagenda)
            INNER JOIN
         pessoa psicologo ON (agenda.pessoa_idpessoa = psicologo.idpessoa)
            INNER JOIN
         pessoa cliente ON (pessoa_agenda.pessoa_idpessoa = cliente.idpessoa)
      WHERE
         pessoa_agenda.pacoteconsulta_idpacoteconsulta IS NOT NULL
            AND pessoa_agenda.pa_status IN (5 , 6, 7, 8)
      ORDER BY pessoa_agenda.pacoteconsulta_idpacoteconsulta , pessoa_agenda.idpessoa_agenda";

const _SQL_SESSION_LIST_BY_TOKEN = "select * from recebimento where rec_gateway_pagto = 2 and "
    . " rec_status_pagto not in (3,4,5,6) and rec_token_gateway = '%s'";

const _SQL_PACKAGE_LIST = 'select distinct pacoteconsulta_idpacoteconsulta packageNumber from pessoa_agenda where pa_status < 9 '
    . 'and pa_data >= NOW() and pa_data <= NOW() + INTERVAL 1 DAY order by pacoteconsulta_idpacoteconsulta';

const _SQL_EMAIL_MONTHLY_REMINDER_BUY_AGAIN =
"SELECT DISTINCT\r\n
   psicologo.idpessoa cd_psicologo,\r\n
   psicologo.pes_nome nm_psicologo,\r\n
   psicologo.pes_login email_psicologo,\r\n
   cliente.pes_nome nm_cliente,\r\n
   cliente.idpessoa cd_cliente,\r\n
   cliente.pes_login email_cliente\r\n
   FROM\r\n
   pessoa_agenda\r\n
      INNER JOIN\r\n
   agenda ON (agenda.idagenda = pessoa_agenda.agenda_idagenda)\r\n
      INNER JOIN\r\n
   pessoa psicologo ON (agenda.pessoa_idpessoa = psicologo.idpessoa)\r\n
      INNER JOIN\r\n
   pessoa cliente ON (pessoa_agenda.pessoa_idpessoa = cliente.idpessoa)\r\n
   WHERE\r\n
   pa_status IN (3 , 4, 5, 6, 7, 8)\r\n
      AND YEAR(pa_data) = YEAR(CURDATE())\r\n
      AND (MONTH(CURDATE()) - MONTH(pa_data)) <= 3\r\n
   ORDER BY cliente.pes_nome";

const _SQL_PAYPAL_TOKEN_TO_CHECK = " select distinct pessoa_agenda_pessoa_idpessoa customerId, rec_token_gateway from "
    . "recebimento where rec_token_gateway is not null and rec_status_pagto not in (3,4,5,6) and  "
    . "rec_data_emissao >= current_date() and  rec_data_emissao <= NOW() + INTERVAL 1 DAY ";
//END: In use in Automatic Monitor

//ConectaEmailService

//END : ConectaEmailService

//AgendaController::getProfessionalAvailable
const _SQL_PROFESSIONAL_AVAIL = "select idpessoa, idagenda, pes_nome, pes_foto, age_data, age_hora from agenda, pessoa where "
    . " pessoa.pes_ativo = 1 and pessoa.pes_tipo = 1 and pessoa.idpessoa = agenda.pessoa_idpessoa and agenda.age_status = 1 and agenda.type = 0 and "
    . " timediff(cast(concat(cast(age_data as char(10)), ' ', cast(age_hora as char(8))) as datetime), NOW()) >= '04:00:00' "
    . " order by age_data, age_hora limit 0, %s ";
