@if($haveOrganizations == 0)
    <div class="empty-organizations-card" style="
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        padding: 2.5rem;
        margin: 2.5rem 0;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 40px rgba(0, 128, 215, 0.08);
        border: 1px solid rgba(0, 128, 215, 0.1);
    ">
        <!-- Фоновые элементы -->
        <div style="
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(0, 128, 215, 0.05) 0%, rgba(0, 128, 215, 0.02) 100%);
            border-radius: 50%;
            transform: translate(30%, -30%);
            z-index: 0;
        "></div>
        
        <div style="
            position: absolute;
            bottom: 0;
            left: 0;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.03) 0%, rgba(0, 0, 0, 0.01) 100%);
            border-radius: 50%;
            transform: translate(-30%, 30%);
            z-index: 0;
        "></div>
        
        <div class="content" style="position: relative; z-index: 1;">
            <!-- Иконка с мягким фоном -->
            <div style="
                display: flex;
                align-items: center;
                gap: 1.25rem;
                margin-bottom: 1.75rem;
            ">
                <div style="
                    width: 72px;
                    height: 72px;
                    background: linear-gradient(135deg, #0080D7 0%, rgba(0, 128, 215, 0.9) 100%);
                    border-radius: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 12px 32px rgba(0, 128, 215, 0.2);
                    position: relative;
                ">
                    <!-- Внутреннее свечение -->
                    <div style="
                        position: absolute;
                        inset: 1px;
                        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
                        border-radius: 19px;
                        z-index: 1;
                    "></div>
                    
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" style="position: relative; z-index: 2;">
                        <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" 
                              stroke="white" 
                              stroke-width="1.5" 
                              stroke-linecap="round" 
                              stroke-linejoin="round"/>
                    </svg>
                </div>
                
                <div>
                    <h3 style="
                        color: #1a1a1a;
                        font-weight: 700;
                        font-size: 1.75rem;
                        margin: 0 0 0.5rem 0;
                        letter-spacing: -0.02em;
                        line-height: 1.2;
                    ">
                        Приветствуем вас! 👋
                    </h3>
                    <p style="
                        color: #666;
                        font-size: 1.0625rem;
                        margin: 0;
                        font-weight: 400;
                    ">
                        Давайте начнем работу с организацией
                    </p>
                </div>
            </div>
            
            <!-- Основной текст -->
            <div style="
                background: linear-gradient(135deg, rgba(0, 128, 215, 0.04) 0%, rgba(0, 128, 215, 0.01) 100%);
                border-radius: 20px;
                padding: 2rem;
                margin-bottom: 2rem;
                border: 1px solid rgba(0, 128, 215, 0.08);
            ">
                <p style="
                    color: #444;
                    font-size: 1.125rem;
                    line-height: 1.7;
                    margin: 0 0 1.5rem 0;
                ">
                    Чтобы управлять услугами, получать заявки от клиентов и раскрыть все возможности платформы, 
                    необходимо <strong style="color: #0080D7;">привязать вашу организацию</strong> к аккаунту.
                </p>
                
                <div style="
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1.25rem;
                    margin-top: 1.5rem;
                ">
                    <div style="
                        background: white;
                        padding: 1.5rem;
                        border-radius: 16px;
                        border: 1px solid #f0f0f0;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
                        transition: all 0.3s ease;
                    " onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0, 128, 215, 0.08)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.03)';">
                        <div style="
                            width: 40px;
                            height: 40px;
                            background: linear-gradient(135deg, rgba(0, 128, 215, 0.1) 0%, rgba(0, 128, 215, 0.05) 100%);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: 1rem;
                        ">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#0080D7">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <p style="color: #333; font-size: 0.9375rem; margin: 0; font-weight: 500;">
                            Управление услугами и ценами
                        </p>
                    </div>
                    
                    <div style="
                        background: white;
                        padding: 1.5rem;
                        border-radius: 16px;
                        border: 1px solid #f0f0f0;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
                        transition: all 0.3s ease;
                    " onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0, 128, 215, 0.08)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.03)';">
                        <div style="
                            width: 40px;
                            height: 40px;
                            background: linear-gradient(135deg, rgba(0, 128, 215, 0.1) 0%, rgba(0, 128, 215, 0.05) 100%);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: 1rem;
                        ">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#0080D7">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </div>
                        <p style="color: #333; font-size: 0.9375rem; margin: 0; font-weight: 500;">
                            Получение заявок от клиентов
                        </p>
                    </div>
                    
                    <div style="
                        background: white;
                        padding: 1.5rem;
                        border-radius: 16px;
                        border: 1px solid #f0f0f0;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
                        transition: all 0.3s ease;
                    " onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0, 128, 215, 0.08)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.03)';">
                        <div style="
                            width: 40px;
                            height: 40px;
                            background: linear-gradient(135deg, rgba(0, 128, 215, 0.1) 0%, rgba(0, 128, 215, 0.05) 100%);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: 1rem;
                        ">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#0080D7">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <p style="color: #333; font-size: 0.9375rem; margin: 0; font-weight: 500;">
                            Продвижение профиля
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Кнопки действий -->
            <div style="
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                align-items: center;
                justify-content: space-between;
                padding-top: 1.5rem;
                border-top: 1px solid #f0f0f0;
            ">
                <div>
                    <a href="{{ route('account.agency.add.organization') }}" 
                       style="
                            background: linear-gradient(135deg, #0080D7 0%, #0066b3 100%);
                            color: white;
                            font-weight: 600;
                            padding: 1rem 2.5rem;
                            border-radius: 14px;
                            text-decoration: none;
                            display: inline-flex;
                            align-items: center;
                            gap: 0.75rem;
                            transition: all 0.3s ease;
                            border: none;
                            cursor: pointer;
                            font-size: 1.0625rem;
                            box-shadow: 0 8px 24px rgba(0, 128, 215, 0.25);
                       "
                       onmouseover="
                           this.style.transform='translateY(-2px)';
                           this.style.boxShadow='0 12px 32px rgba(0, 128, 215, 0.35)';
                           this.style.background='linear-gradient(135deg, #0090E7 0%, #0076c3 100%)';
                       "
                       onmouseout="
                           this.style.transform='translateY(0)';
                           this.style.boxShadow='0 8px 24px rgba(0, 128, 215, 0.25)';
                           this.style.background='linear-gradient(135deg, #0080D7 0%, #0066b3 100%)';
                       ">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="stroke: white;">
                            <path d="M12 5v14m-7-7h14"/>
                        </svg>
                        Привязать организацию
                    </a>
                </div>
                
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <span style="color: #888; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4m0-4h.01"/>
                        </svg>
                        Нужна помощь?
                    </span>
                    <a
                       style="
                            color: #666;
                            font-weight: 500;
                            padding: 0.75rem 1.5rem;
                            border-radius: 12px;
                            text-decoration: none;
                            display: inline-flex;
                            align-items: center;
                            gap: 0.5rem;
                            transition: all 0.2s ease;
                            border: 1px solid #e0e0e0;
                            background: white;
                       "
                       onmouseover="this.style.backgroundColor='#f8f8f8'; this.style.color='#333'; this.style.borderColor='#0080D7';"
                       onmouseout="this.style.backgroundColor='white'; this.style.color='#666'; this.style.borderColor='#e0e0e0';">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Инструкция
                    </a>
                </div>
            </div>
            
            <!-- Прогресс бар для визуализации -->
            <div style="
                margin-top: 2rem;
                padding: 1rem;
                background: linear-gradient(135deg, rgba(0, 128, 215, 0.04) 0%, rgba(0, 128, 215, 0.01) 100%);
                border-radius: 16px;
                border: 1px solid rgba(0, 128, 215, 0.08);
            ">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #444; font-size: 0.875rem; font-weight: 500;">
                        Шаг 1 из 2
                    </span>
                    <span style="color: #0080D7; font-size: 0.875rem; font-weight: 600;">
                        50% завершено
                    </span>
                </div>
                <div style="
                    width: 100%;
                    height: 8px;
                    background: rgba(0, 0, 0, 0.05);
                    border-radius: 4px;
                    overflow: hidden;
                ">
                    <div style="
                        width: 50%;
                        height: 100%;
                        background: linear-gradient(90deg, #0080D7 0%, rgba(0, 128, 215, 0.7) 100%);
                        border-radius: 4px;
                    "></div>
                </div>
                <p style="color: #666; font-size: 0.8125rem; margin: 0.5rem 0 0 0;">
                    Вы создали аккаунт. Теперь привяжите организацию для полного доступа
                </p>
            </div>
        </div>
    </div>
@endif