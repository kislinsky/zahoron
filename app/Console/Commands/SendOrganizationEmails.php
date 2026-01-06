<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendOrganizationEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-to-organizations 
                            {--limit=100 : Лимит организаций для отправки за один запуск}
                            {--test : Отправить тестовое письмо на указанный email}
                            {--email= : Email для тестовой отправки}
                            {--resend : Отправить всем организациям повторно}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправка email рассылки организациям';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       

        $limit = (int)$this->option('limit');
        $resend = $this->option('resend');
        
        // Получаем организации
        $query = Organization::whereNotNull('email')
            ->where('email', '!=', '');
        
        $organizations = $query->limit($limit)->get();
        
        if ($organizations->isEmpty()) {
            $this->info('Нет организаций для отправки email.');
            return 0;
        }
        
        $this->info("Начинаю отправку email для {$organizations->count()} организаций...");
        
        $successCount = 0;
        $failCount = 0;
        
        $progressBar = $this->output->createProgressBar($organizations->count());
        $progressBar->start();
        
        foreach ($organizations as $organization) {
            $emailBody = $this->getEmailTemplate($organization);
            $subject = "Приглашение присоединиться к платформе zahoron.ru";
            
            $success = sendMail(
                'toni.vinogradov.06@inbox.ru',
                $subject,
                $emailBody,
                true
            );
            
            if ($success) {
                
                $successCount++;
            } else {
                $this->error("Ошибка отправки для: {$organization->email}");
                $failCount++;
            }
            
            $progressBar->advance();
            
            // Пауза между отправками, чтобы не перегружать SMTP
            usleep(100000); // 0.1 секунда
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Отправка завершена!");
        $this->info("Успешно отправлено: {$successCount}");
        $this->info("Не удалось отправить: {$failCount}");
        
        return 0;
    }
  
private function getEmailTemplate($organization)
{
    $organizationName = $organization->title ?: 'Уважаемая организация';
    $city = $organization->city ?? 'Иваново';
    
return <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zahoron.ru - Платформа ритуальных услуг</title>
</head>
<body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; background-color: #f0f0f0; color: #1A1A1A; line-height: 1.5;">
    <div style="width: 100%; max-width: 550px; background: #F7F7F8; margin: 0 auto; border-radius: 8px; overflow: hidden; position: relative;">
        <!-- Логотип -->
        <img src="https://zahoron.ru/storage/uploads/ZAHORON.RU.png" 
             alt="Zahoron.ru"
             style="width: 90%; height: auto; padding: 10px; display: block; margin: 0 auto;"
             width="200" 
             height="50">
        
        <!-- Меню -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 10px; margin: 10px; padding: 10px 0;">
            <a href="https://zahoron.ru/{$city->slug}" 
               style="padding: 8px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; font-size: 14px; font-weight: 600; color: #1A1A1A; text-decoration: none; background: white; border-radius: 4px; border: none; white-space: nowrap;">
                <img src="https://zahoron.ru/storage/uploads/Vector.png" alt="Zahoron.ru" width="11" height="14" style="display: block;">{$city->title}
            </a>
            <a href="https://zahoron.ru/{$city->slug}/organizations/organizacia-pohoron" 
               style="padding: 8px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; font-size: 14px; font-weight: 600; color: #1A1A1A; text-decoration: none; background: white; border-radius: 4px; border: none; white-space: nowrap;">
                Ритуальные услуги
            </a>
            <a href="https://zahoron.ru/{$city->slug}/organizations/pamatniki" 
               style="padding: 8px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; font-size: 14px; font-weight: 600; color: #1A1A1A; text-decoration: none; background: white; border-radius: 4px; border: none; white-space: nowrap;">
                Облагораживание
            </a>
            <a href="https://zahoron.ru/{$city->slug}/cemeteries" 
               style="padding: 8px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; font-size: 14px; font-weight: 600; color: #1A1A1A; text-decoration: none; background: white; border-radius: 4px; border: none; white-space: nowrap;">
                Кладбища
            </a>
            <a href="https://zahoron.ru/{$city->slug}/kontakty" 
               style="padding: 8px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; font-size: 14px; font-weight: 600; color: #1A1A1A; text-decoration: none; background: white; border-radius: 4px; border: none; white-space: nowrap;">
                Информация
            </a>
        </div>
        
        <!-- Синий блок -->
        <div style="background: #0097FE; display: flex;  gap: 0px;  border-radius: 8px;  position: relative;">
            <img src="https://zahoron.ru/storage/uploads/image%202%20(3).png" 
                 alt="Иконка платформы"
                 style="max-width: 270px; height: 130px; display: block; flex-shrink: 0;"
                 width="150" 
                 height="150">
            <div style="margin: 10px 0; margin-right: 20px; font-weight: 700; font-family: Times New Roman, serif; font-size: 20px; color: white; line-height: 1.3; flex-grow: 1;">
                Платформа, где организации находят друг друга без лишних звонков и писем.
            </div>
        </div>
        
        <!-- Текст письма -->
        <div style="font-weight: 600; font-size: 16px; padding: 20px; color: #1A1A1A;">
            Здравствуйте, {$organizationName}!<br><br>
            Мы запускаем платформу, цель которой — сделать рынок ритуальных услуг более открытым и понятным для людей в сложные моменты утраты.<br><br>
            Проект объединяет актуальные и сравнимые цены на ритуальные услуги и формируется в том числе на основе данных самих организаций.<br><br>
            Приглашаем вас зарегистрироваться и привязать вашу организацию, чтобы информация о ваших услугах была представлена корректно и прозрачно.
            Регистрация занимает несколько минут.<br><br>
            👉 <a href="https://zahoron.ru" style="color: #0097FE; text-decoration: none; text-transform: uppercase; font-weight: 600;">zahoron.ru</a><br><br>
            С уважением,<br>Команда проекта zahoron.ru
        </div>
        
        <!-- Кнопка регистрации -->
        <a href="https://zahoron.ru/{$city->slug}/register" 
           style="width: calc(100% - 40px); max-width: 411px; background: #0097FE; display: block; margin: 20px auto; text-align: center; font-weight: 600; font-size: 18px; color: white; text-decoration: none; padding: 18px; border-radius: 0px; position: relative; z-index: 2;">
            Зарегистрироваться
        </a>
        
        <!-- Социальные сети -->
        <div style="display: flex; align-items: center; justify-content: center; gap: 15px; padding: 20px; margin-top: 20px; position: relative; z-index: 2;">
            <a href="https://vk.com/zahoron_ru" 
               style="display: flex; align-items: center; justify-content: center; width: 40px; border-radius: 50%; height: 40px; background: #0097FE; text-decoration: none;">
                <img src="https://zahoron.ru/storage/uploads/Vector%20(1).png" 
                     alt="VK" 
                     style="width: 20px; height: 20px; display: block;"
                     width="20" 
                     height="20">
            </a>
            <a href="https://vk.com/zahoron_ru" 
               style="display: flex; align-items: center; justify-content: center; width: 40px; border-radius: 50%; height: 40px; background: #0097FE; text-decoration: none;">
                <img src="https://zahoron.ru/storage/uploads/Vector%20(2).png" 
                     alt="VK" 
                     style="width: 20px; height: 20px; display: block;"
                     width="20" 
                     height="20">
            </a>
            <a href="https://vk.com/zahoron_ru" 
               style="display: flex; align-items: center; justify-content: center; width: 40px; border-radius: 50%; height: 40px; background: #0097FE; text-decoration: none;">
                <img src="https://zahoron.ru/storage/uploads/Vector%20(3).png" 
                     alt="VK" 
                     style="width: 20px; height: 20px; display: block;"
                     width="20" 
                     height="20">
            </a>
            <a href="https://vk.com/zahoron_ru" 
               style="display: flex; align-items: center; justify-content: center; width: 40px; border-radius: 50%; height: 40px; background: #0097FE; text-decoration: none;">
                <img src="https://zahoron.ru/storage/uploads/Vector%20(4).png" 
                     alt="VK" 
                     style="width: 20px; height: 20px; display: block;"
                     width="20" 
                     height="20">
            </a>
            <a href="https://vk.com/zahoron_ru" 
               style="display: flex; align-items: center; justify-content: center; width: 40px; border-radius: 50%; height: 40px; background: #0097FE; text-decoration: none;">
                <img src="https://zahoron.ru/storage/uploads/Vector%20(5).png" 
                     alt="VK" 
                     style="width: 20px; height: 20px; display: block;"
                     width="20" 
                     height="20">
            </a>
        </div>
        
        <!-- Фото в самом низу блока (position absolute) -->
        <img src="https://zahoron.ru/storage/uploads/Frame%20222.png" 
             alt=""
             style="position: absolute; bottom: 0; left: 0; width: 100%; display: block; z-index: 0;">
    </div>
</body>
</html>
HTML;
    
}

}