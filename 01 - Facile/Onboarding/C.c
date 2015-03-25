#include <stdlib.h>
#include <stdio.h>
#include <string.h>

int main()
{

    // game loop
    while (1) {
        char enemy1[256];
        scanf("%s", enemy1);
        int dist1;
        scanf("%d", &dist1);
        char enemy2[256];
        scanf("%s", enemy2);
        int dist2;
        scanf("%d", &dist2);
        
        if (dist1 <= dist2) {
            printf(strcat(enemy1, "\n"));
        } else {
            printf(strcat(enemy2, "\n"));
        }
    }
}