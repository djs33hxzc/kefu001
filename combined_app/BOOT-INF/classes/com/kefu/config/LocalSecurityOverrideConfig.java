package com.kefu.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.core.annotation.Order;
import org.springframework.security.config.Customizer;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.config.annotation.web.configurers.AbstractHttpConfigurer;
import org.springframework.security.web.SecurityFilterChain;

@Configuration
@EnableWebSecurity
public class LocalSecurityOverrideConfig {

    @Bean
    @Order(0)
    SecurityFilterChain localStaticSecurityFilterChain(HttpSecurity http) throws Exception {
        http
            .securityMatcher(
                "/",
                "/index.html",
                "/favicon.ico",
                "/manifest.webmanifest",
                "/sw.js",
                "/404.html",
                "/图标.png",
                "/css/**",
                "/js/**",
                "/kjs-assets/**",
                "/landing/**"
            )
            .authorizeHttpRequests(auth -> auth.anyRequest().permitAll())
            .csrf(AbstractHttpConfigurer::disable)
            .cors(Customizer.withDefaults());
        return http.build();
    }
}
