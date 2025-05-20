// components/navigation/bottom-nav.tsx
"use client";

import React from "react";
import Link from "next/link";
import {
  HomeIcon,
  ListIcon,
  TrophyIcon,
  SearchIcon,
  UserIcon,
} from "lucide-react";
import { usePathname } from "next/navigation";

interface BottomNavProps {
  locale: string;
}

export function BottomNav({ locale }: BottomNavProps) {
  const pathname = usePathname();

  const navItems = [
    {
      label: "Home",
      href: `/${locale}`,
      icon: HomeIcon,
    },
    {
      label: "Races",
      href: `/${locale}/races`,
      icon: ListIcon,
    },
    {
      label: "Records",
      href: `/${locale}/records`,
      icon: TrophyIcon,
    },
    {
      label: "Search",
      href: `/${locale}/search`,
      icon: SearchIcon,
    },
    {
      label: "Profile",
      href: `/${locale}/profile`,
      icon: UserIcon,
    },
  ];

  return (
    <nav className="fixed bottom-0 left-0 right-0 z-40 md:hidden bg-card shadow-[0_-2px_8px_rgba(0,0,0,0.1)]">
      <div className="grid grid-cols-5 h-16">
        {navItems.map((item, index) => {
          const Icon = item.icon;
          const isActive = pathname === item.href;

          return (
            <Link
              key={index}
              href={item.href}
              className={`flex flex-col items-center justify-center ${
                isActive
                  ? "text-primary"
                  : "text-muted-foreground hover:text-card-foreground"
              }`}
            >
              <Icon
                className={`h-5 w-5 ${isActive ? "stroke-[2.5px]" : "stroke-[1.5px]"}`}
              />
              <span className="text-[10px] mt-1">{item.label}</span>
            </Link>
          );
        })}
      </div>
    </nav>
  );
}
