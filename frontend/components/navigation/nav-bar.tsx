"use client";

import React, { useState } from "react";
import Link from "next/link";
import ThemeToggle from "@/theme/theme-toggle";

export interface NavItem {
  label: string;
  href?: string;
  children?: NavItem[];
}

interface NavBarProps {
  items: NavItem[];
}

export function NavBar({ items }: NavBarProps) {
  const [openIndex, setOpenIndex] = useState<number | null>(null);

  return (
    <nav className="hidden md:flex items-center space-x-6 p-4 shadow-md bg-[var(--color-card)] text-[var(--color-card-foreground)]">
      {items.map((item, index) => (
        <div
          key={index}
          className="relative"
          onMouseEnter={() => setOpenIndex(index)}
          onMouseLeave={() => setOpenIndex(null)}
        >
          {item.href ? (
            <Link href={item.href} className="hover:underline">
              {item.label}
            </Link>
          ) : (
            <span className="text-inherit">{item.label}</span>
          )}

          {item.children && openIndex === index && (
            <div className="absolute left-0 mt-2 w-48 bg-[var(--color-card)] border border-[var(--color-border)] rounded shadow-lg z-10">
              {item.children.map((child, idx) => (
                <Link
                  key={idx}
                  href={child.href || "#"}
                  className="block px-4 py-2 text-[var(--color-foreground)] hover:bg-[var(--color-muted)]"
                >
                  {child.label}
                </Link>
              ))}
            </div>
          )}
        </div>
      ))}

      <ThemeToggle />
    </nav>
  );
}
